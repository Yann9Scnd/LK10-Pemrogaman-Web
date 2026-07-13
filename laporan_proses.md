# LAPORAN PROSES PERBAIKAN PROYEK & REFACTORING KODE
## MATA KULIAH: PEMROGRAMAN WEB / KEAMANAN INFORMASI

---

### IDENTITAS MAHASISWA
* **Nama Lengkap** : Muhammad Mardiansyah
* **NIM**           : [Masukkan NIM Anda di Sini]
* **Kelas**         : [Masukkan Kelas Anda di Sini]
* **Proyek Web**    : **Secure App** (Laravel 12 + WorkOS SSO)
* **Repositori**    : https://github.com/Yann9Scnd/LK10-Pemrogaman-Web.git

---

## I. BUG FIX LOG (MINIMAL 3 MASALAH)

### 1. Bug 1: Loop Redirect pada Kegagalan Autentikasi (Logical Bug)
* **Deskripsi Masalah:**
  Saat proses callback WorkOS gagal (misal: kode otentikasi tidak valid atau dibatalkan), controller mengarahkan pengguna kembali ke rute `login` dengan perintah:
  `return redirect()->route('login')->with('error', ...);`
  Namun, rute `login` adalah pintu gerbang menuju authorize URL WorkOS. Akibatnya, pengguna dialihkan kembali ke halaman eksternal WorkOS secara instan, memicu **infinite redirect loop** (lingkaran tak berujung). Pengguna tidak pernah bisa melihat pesan error di halaman lokal.
  
* **Sebelum Perbaikan (`AuthController.php`):**
  ```php
  if (!$code) {
      return redirect()->route('login')->with('error', 'Authentication code not provided.');
  }
  ```
  
* **Setelah Perbaikan (`AuthController.php`):**
  ```php
  if (!$code) {
      return redirect()->route('login.page')->with('error', 'Authentication code not provided.');
  }
  ```
  *(Hal ini juga diterapkan pada seluruh blok error handler di callback)*
  
* **Dampak Perbaikan:** Rantai putaran terputus. Jika terjadi kegagalan, pengguna diarahkan dengan aman ke halaman lokal `/login-page` (Gateway Keamanan) dan pesan kesalahan ditampilkan dengan benar pada antarmuka pengguna.

---

### 2. Bug 2: Penggunaan Fungsi `env()` Secara Langsung di Controller (Architectural Bug)
* **Deskripsi Masalah:**
  Nilai konfigurasi kredensial WorkOS diambil langsung menggunakan helper `env()` di dalam method controller. Dalam arsitektur Laravel, ketika perintah optimasi `php artisan config:cache` dijalankan di server produksi, Laravel tidak akan membaca berkas `.env` secara dinamis. Panggilan `env()` di luar berkas konfigurasi akan selalu mengembalikan nilai `null`, yang menyebabkan integrasi WorkOS mendadak tidak berfungsi (broken).
  
* **Sebelum Perbaikan (`AuthController.php`):**
  ```php
  $clientId = env('WORKOS_CLIENT_ID');
  $apiKey = env('WORKOS_API_KEY');
  $redirectUri = env('WORKOS_REDIRECT_URI');
  ```
  
* **Setelah Perbaikan:**
  1. *Daftarkan konfigurasi di `config/services.php`:*
     ```php
     'workos' => [
         'client_id'     => env('WORKOS_CLIENT_ID'),
         'api_key'       => env('WORKOS_API_KEY'),
         'redirect_uri'  => env('WORKOS_REDIRECT_URI'),
     ],
     ```
  2. *Panggil konfigurasi di `AuthController.php` menggunakan helper `config()`:*
     ```php
     $clientId = config('services.workos.client_id');
     $apiKey = config('services.workos.api_key');
     $redirectUri = config('services.workos.redirect_uri');
     ```
     
* **Dampak Perbaikan:** Menjaga keandalan konfigurasi sistem. Kode sekarang aman dari kehilangan nilai saat cache konfigurasi diaktifkan pada server produksi.

---

### 3. Bug 3: Ketiadaan Validasi Token State pada Callback OAuth 2.0 (Security Vulnerability - CSRF)
* **Deskripsi Masalah:**
  Alur otentikasi login sebelumnya tidak menggunakan parameter `state`. Tanpa validasi token `state`, aplikasi rentan terhadap serangan **OAuth Cross-Site Request Forgery (CSRF)**. Penyerang dapat mengelabui korban untuk menyelesaikan alur login menggunakan kredensial milik penyerang, yang dapat mengarah pada asosiasi data sensitif korban ke akun penyerang.
  
* **Sebelum Perbaikan (`AuthController.php`):**
  * *Rute login langsung menuju otorisasi tanpa parameter state:*
    ```php
    $query = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'provider'      => 'authkit',
        'screen_hint'   => 'sign-in',
    ]);
    ```
  * *Callback tidak memvalidasi token state apa pun.*
  
* **Setelah Perbaikan (`AuthController.php`):**
  * *Membuat token state acak di session dan mengirimkannya:*
    ```php
    $state = Str::random(40);
    $request->session()->put('oauth_state', $state);
    
    $query = http_build_query([
        // ... parameter lainnya
        'state' => $state,
    ]);
    ```
  * *Memvalidasi kecocokan token state pada saat callback:*
    ```php
    $state = $request->query('state');
    $savedState = $request->session()->pull('oauth_state');
    
    if (!$state || !$savedState || $state !== $savedState) {
        return redirect()->route('login.page')->with('error', 'Invalid state token. Possible CSRF attack detected.');
    }
    ```
    
* **Dampak Perbaikan:** Mengamankan transaksi otentikasi dari serangan pembajakan sesi OAuth CSRF, menjamin validitas dari permintaan otorisasi pengguna.

---

## II. REFACTORING LOG (MINIMAL 2 BAGIAN KODE)

### 1. Refactoring 1: Ekstraksi Logika Dekode Payload JWT (Code Extraction)
* **Latar Belakang:**
  Saat memproses callback dari WorkOS, token akses berupa JWT diurai untuk mendapatkan *Session ID* (`sid`). Sebelumnya, pemisahan string token dan operasi dekode base64 ditulis secara langsung (inline) di dalam method `callback`. Penulisan inline ini membuat method utama menjadi sangat panjang dan sulit dibaca.
  
* **Sebelum Refactoring (`AuthController.php`):**
  ```php
  $sessionId = null;
  if (!empty($data['access_token'])) {
      $parts = explode('.', $data['access_token']);
      if (count($parts) === 3) {
          $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
          $sessionId = $payload['sid'] ?? null;
      }
  }
  ```
  
* **Setelah Refactoring (`AuthController.php`):**
  Logika tersebut diekstrak ke dalam method private helper khusus, lengkap dengan optimasi padding base64 URL:
  ```php
  // Panggilan di method callback
  $sessionId = null;
  if (!empty($data['access_token'])) {
      $payload = $this->decodeJwtPayload($data['access_token']);
      $sessionId = $payload['sid'] ?? null;
  }
  
  // Method pembantu baru (helper)
  private function decodeJwtPayload(string $token): ?array
  {
      $parts = explode('.', $token);
      if (count($parts) !== 3) {
          return null;
      }
      $base64 = strtr($parts[1], '-_', '+/');
      $padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);
      $decoded = base64_decode($padded);
      return $decoded ? json_decode($decoded, true) : null;
  }
  ```
  
* **Alasan Refactoring:** Meningkatkan keterbacaan kode (*readability*) dan kemudahan pemeliharaan (*maintainability*). Method callback menjadi lebih ringkas dan fokus pada alur kontrol otentikasi utama.

---

### 2. Refactoring 2: Pemindahan Logika Validasi Input ke Kelas Form Request (Separation of Concerns)
* **Latar Belakang:**
  Validasi form feedback sebelumnya dilakukan secara inline di dalam method `submitForm` pada `DashboardController.php`. Ini melanggar prinsip *Single Responsibility Principle (SRP)* karena controller ikut dibebani tanggung jawab mendefinisikan aturan validasi dan kustomisasi pesan kesalahan input.
  
* **Sebelum Refactoring (`DashboardController.php`):**
  ```php
  public function submitForm(Request $request)
  {
      $validated = $request->validate([
          'name' => 'required|string|min:3|max:50',
          'email' => 'required|email|max:100',
          'feedback' => 'required|string|min:10|max:1000',
      ]);
      // ... proses selanjutnya
  }
  ```
  
* **Setelah Refactoring:**
  1. *Membuat kelas khusus `FeedbackRequest` di `app/Http/Requests/FeedbackRequest.php` untuk menampung aturan validasi dan kustomisasi pesan kesalahan (error messages).*
  2. *Mengubah controller agar menggunakan data yang ter-type-hint secara bersih:*
     ```php
     use App\Http\Requests\FeedbackRequest;
     
     public function submitForm(FeedbackRequest $request)
     {
         $validated = $request->validated();
         return back()->with('success_feedback', 'Feedback Anda berhasil dikirim dengan aman!');
     }
     ```
     
* **Alasan Refactoring:** Penerapan *Separation of Concerns* (pemisahan perhatian). Controller sekarang hanya peduli pada aksi bisnis utama, sedangkan aturan validasi dapat dikelola secara terpisah, modular, dan siap digunakan kembali di tempat lain jika diperlukan.

---

## III. AI USAGE LOG (LOG PENGGUNAAN ASISTEN AI)

Berikut adalah catatan interaksi dengan asisten AI (Antigravity) dalam proses analisis bug dan pemrosesan refactoring proyek:

| Sesi Aktivitas | Masukan dari Pengguna (Prompt) | Analisis & Solusi dari AI | Hasil/Tindakan Implementasi |
| :--- | :--- | :--- | :--- |
| **Sesi 1** <br>*(Identifikasi Bug)* | *"Tolong periksa file AuthController.php. Apakah ada celah keamanan atau potensi bug logis yang bisa merusak jalannya aplikasi?"* | AI mengidentifikasi ketiadaan validasi `state` pada otentikasi OAuth 2.0 (menyebabkan celah OAuth CSRF) dan adanya masalah loop redirect di mana rute error mengarah ke rute inisiasi login. | Melakukan pembaruan alur inisiasi login dengan menyisipkan parameter `state` dan memperbarui rute redirect gagal ke halaman utama lokal (`login.page`). |
| **Sesi 2** <br>*(Analisis Konfigurasi)* | *"Kenapa disarankan memindahkan helper env() ke config/services.php?"* | AI menjelaskan dampak dari fitur `config:cache` pada Laravel di mana file `.env` tidak akan dibaca di lingkungan produksi, sehingga semua panggilan langsung ke `env()` akan menghasilkan `null`. | Membuat entri konfigurasi baru `'workos'` di file `config/services.php` dan mengganti semua pemanggilan `env()` di controller dengan `config()`. |
| **Sesi 3** <br>*(Refactoring)* | *"Bagaimana cara merapikan validasi input di DashboardController agar controller-nya bersih?"* | AI menyarankan membuat kelas Form Request khusus (`FeedbackRequest`) menggunakan library Laravel, memisahkan aturan otorisasi dan kustomisasi pesan kesalahan. | Membuat berkas request baru `FeedbackRequest.php` dan menerapkan injeksi ketergantungan (dependency injection) pada method `submitForm` di controller. |
