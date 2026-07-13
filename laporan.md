# LAPORAN TUGAS PRAKTIKUM KEAMANAN WEB
## INTEGRASI AUTHENTICATION, ROUTE PROTECTION, & INPUT VALIDATION

---

### IDENTITAS MAHASISWA
* **Nama Lengkap** : Muhammad Mardiansyah
* **NIM**           : [Masukkan NIM Anda di Sini]
* **Kelas**         : [Masukkan Kelas Anda di Sini]
* **Mata Kuliah**   : Pemrograman Web / Keamanan Informasi
* **Proyek Web**    : **Secure App** (Laravel 12 + WorkOS SSO)
* **Repositori**    : https://github.com/Yann9Scnd/LK10-Pemrogaman-Web.git

---

## 1. Deskripsi Proyek
**Secure App** adalah aplikasi web berbasis framework **Laravel 12** yang berfokus pada implementasi autentikasi aman dengan integrasi **WorkOS AuthKit (Single Sign-On)**. Aplikasi ini dirancang untuk mendemonstrasikan praktik terbaik keamanan web seperti proteksi rute dengan middleware, validasi input berlapis di sisi server, pencegahan kerentanan web populer (XSS & SQL Injection), serta penyediaan endpoint API terproteksi dalam format JSON.

---

## 2. Implementasi Autentikasi Login Dasar (WorkOS SSO)
Autentikasi pada Secure App menggunakan protokol Single Sign-On (SSO) berbasis OAuth 2.0 melalui layanan **WorkOS AuthKit**. Pengguna diarahkan ke portal resmi WorkOS untuk verifikasi identitas secara aman tanpa perlu menyimpan kredensial password langsung di database lokal aplikasi.

* **Alur Logika Autentikasi:**
  1. Pengguna mengakses `/login-page` (Tampilan Gateway).
  2. Klik tombol login mengarah ke `/login` yang memicu controller untuk melakukan redirect ke WorkOS portal.
  3. Setelah sukses, WorkOS mengirimkan *auth code* kembali ke rute callback `/auth/callback` aplikasi untuk memproses login sesi pengguna.

---

## 3. Proteksi Rute (Route Protection)
Aplikasi membatasi akses ke halaman-halaman sensitif sehingga hanya pengguna terautentikasi (telah login) yang dapat masuk. Proteksi ini diterapkan menggunakan middleware `auth` bawaan Laravel pada file rute.

* **Implementasi Kode Rute (`routes/web.php`):**
```php
// Rute Publik (Dapat diakses tanpa login)
Route::get('/login-page', function () {
    return view('login');
})->name('login.page');

// Rute Terproteksi (Hanya dapat diakses setelah login via middleware 'auth')
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/submit', [DashboardController::class, 'submitForm'])->name('dashboard.submit');
    
    // Endpoint API JSON Terproteksi
    Route::get('/api/user', function () {
        return response()->json([
            'success' => true,
            'message' => 'Detail profil user yang sedang login',
            'data' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]
        ]);
    })->name('api.user');
});
```

* **Mekanisme Keamanan:** Jika pengguna yang belum login mencoba mengakses `/dashboard` atau `/api/user`, middleware `auth` secara otomatis akan memblokir permintaan dan mengalihkan pengguna kembali ke halaman login.

---

## 4. Validasi Input & Analisis Risiko Keamanan (SQLi & XSS)

### A. Kode Validasi Input (`DashboardController.php`)
Setiap data masukan dari form feedback divalidasi dengan ketat di sisi server (backend) sebelum diproses untuk meminimalkan data sampah atau injeksi kode berbahaya:
```php
public function submitForm(Request $request)
{
    // Menerapkan validasi input yang ketat
    $validated = $request->validate([
        'name'     => 'required|string|min:3|max:50',
        'email'    => 'required|email|max:100',
        'feedback' => 'required|string|min:10|max:1000',
    ]);
    
    return back()->with('success_feedback', 'Feedback Anda berhasil dikirim dengan aman!');
}
```

### B. Analisis Risiko SQL Injection (SQLi)
* **Definisi:** SQL Injection terjadi saat penyerang memasukkan input berupa karakter perintah SQL (seperti `' OR '1'='1`) ke dalam formulir aplikasi untuk memanipulasi database.
* **Potensi Risiko:** Kebocoran data pengguna, modifikasi tabel database, bypass login, hingga kerusakan basis data total.
* **Pencegahan pada Secure App:** Aplikasi menggunakan **Eloquent ORM** bawaan Laravel. Eloquent secara otomatis menggunakan **PDO Parameter Binding** saat berinteraksi dengan database. Parameter binding memastikan input pengguna dianggap sebagai *literal/data biasa*, bukan sebagai query SQL aktif yang bisa dieksekusi.

### C. Analisis Risiko Cross-Site Scripting (XSS)
* **Definisi:** XSS terjadi ketika aplikasi menerima input berupa kode skrip (seperti JavaScript `<script>alert('hack')</script>`) dan langsung menampilkannya kembali di halaman web tanpa proses penyaringan (*escape*).
* **Potensi Risiko:** Pencurian cookie sesi (Session Hijacking), pembajakan akun, pengalihan paksa halaman web, hingga *deface* tampilan.
* **Pencegahan pada Secure App:** Tampilan dashboard menggunakan **Blade Engine** Laravel dengan sintaks kurung kurawal ganda `{{ $data }}`. Sintaks ini secara otomatis memanggil fungsi PHP `htmlspecialchars()` untuk mengubah karakter khusus menjadi entitas HTML aman (contoh: `<` berubah menjadi `&lt;`), mencegah skrip dieksekusi di browser klien.

---

## 5. Endpoint API Sederhana (JSON)
Secure App menyediakan satu endpoint API internal yang terproteksi middleware di rute `/api/user`. Endpoint ini mengembalikan data identitas pengguna yang sedang aktif dalam format JSON.

* **Format Respon JSON yang Dihasilkan:**
```json
{
  "success": true,
  "message": "Detail profil user yang sedang login",
  "data": {
    "id": "user_01HGPXQ89M...",
    "name": "Muhammad Mardiansyah",
    "email": "mardiansyahiyan2005@gmail.com"
  }
}
```

---

## 6. Checklist Keamanan Dasar Aplikasi

Berikut adalah evaluasi checklist keamanan dasar yang berhasil diimplementasikan pada **Secure App**:

| No | Poin Checklist Keamanan | Status | Keterangan / Implementasi |
| :--- | :--- | :--- | :--- |
| 1 | **Validasi Sisi Server** |  `[x] Terpenuhi` | Validasi tipe data, panjang string, dan format email di controller. |
| 2 | **Proteksi CSRF** |  `[x] Terpenuhi` | Directive `@csrf` pada form POST mencegah pemalsuan permintaan antar-situs. |
| 3 | **Route Protection** |  `[x] Terpenuhi` | Rute dashboard dan API dilindungi oleh middleware `auth`. |
| 4 | **Pencegahan SQLi** |  `[x] Terpenuhi` | Query database diproses secara aman menggunakan Eloquent ORM. |
| 5 | **Pencegahan XSS** |  `[x] Terpenuhi` | Output data pengguna dilewati filter encoding otomatis melalui Blade `{{ }}`. |
| 6 | **Enkripsi Kredensial** |  `[x] Terpenuhi` | Menggunakan layanan WorkOS yang tersertifikasi SOC2 untuk penanganan login. |
| 7 | **Proteksi Sesi Sesi** |  `[x] Terpenuhi` | Manajemen sesi menggunakan cookie terenkripsi bawaan Laravel. |
