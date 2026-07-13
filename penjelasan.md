# Penjelasan Keamanan & Checklist Keamanan Dasar

Dokumen ini disusun oleh **Muhammad Mardiansyah** guna memberikan penjelasan mengenai potensi risiko keamanan (SQL Injection & XSS) serta menyajikan checklist keamanan dasar pada aplikasi Laravel.

---

## 1. Penjelasan Risiko Keamanan (Awareness Level)

### A. SQL Injection (SQLi)
* **Apa itu?** 
  SQL Injection terjadi ketika input pengguna yang tidak divalidasi/difilter digabungkan secara langsung ke dalam query database SQL. Hal ini memungkinkan penyerang untuk memanipulasi struktur query SQL asli untuk membaca, mengubah, atau menghapus data sensitif di database.
* **Potensi Risiko:** 
  Kebocoran data kredensial, modifikasi data tanpa izin, bypass autentikasi login, hingga penghapusan seluruh tabel basis data.
* **Pencegahan di Laravel:**
  Laravel menggunakan **PDO Parameter Binding** secara default di semua query yang dibuat melalui Eloquent ORM atau Query Builder. Parameter binding memastikan input pengguna diperlakukan sebagai literal/data biasa, bukan sebagai perintah SQL yang dapat dieksekusi.
  * *Aman (Eloquent):* `User::where('email', $request->email)->first();`
  * *Bahaya (Hindari Raw Query Tanpa Binding):* `DB::select("SELECT * FROM users WHERE email = '$request->email'");`

---

### B. Cross-Site Scripting (XSS)
* **Apa itu?**
  XSS terjadi ketika aplikasi menerima data berbahaya dari pengguna dan menampilkannya di halaman web tanpa penyaringan/escape. Penyerang menyisipkan skrip jahat (biasanya JavaScript) yang kemudian dieksekusi di peramban (browser) pengguna lain.
* **Potensi Risiko:**
  Pencurian cookie sesi (session hijacking), pengalihan halaman secara paksa, perusakan tampilan situs web (defacement), hingga eksekusi aksi atas nama pengguna lain.
* **Pencegahan di Laravel:**
  Laravel Blade Engine secara default meloloskan (escapes) semua output html menggunakan fungsi PHP `htmlspecialchars` jika kita menggunakan sintaks kurung kurawal ganda `{{ ... }}`.
  * *Aman:* `{{ $userInput }}` (Mengubah `<script>` menjadi `&lt;script&gt;`)
  * *Bahaya:* `{!! $userInput !!}` (Menampilkan HTML secara mentah, hanya gunakan jika input tersebut berasal dari editor kaya/rich-text editor tepercaya dan telah dibersihkan menggunakan library seperti HTMLPurifier).

---

## 2. Checklist Keamanan Dasar Aplikasi Laravel

Berikut adalah checklist keamanan yang diimplementasikan dan direkomendasikan untuk aplikasi ini:

### [x] 1. Validasi Input yang Ketat
- [x] Menggunakan Laravel Form Request Validation (`$request->validate()`) di `DashboardController`.
- [x] Memastikan tipe data, batas minimum/maksimum karakter, serta format email sesuai dengan aturan validasi.

### [x] 2. Proteksi Cross-Site Request Forgery (CSRF)
- [x] Menyertakan directive `@csrf` pada setiap formulir POST di view Blade. Laravel memverifikasi token ini secara otomatis melalui middleware `VerifyCsrfToken` / `ValidateCsrfToken` bawaan.

### [x] 3. Proteksi Akses Rute (Authentication & Middleware)
- [x] Menggunakan middleware `auth` bawaan Laravel untuk memproteksi halaman `/dashboard` dan rute API internal.
- [x] Memastikan pengguna yang belum login diarahkan kembali secara otomatis ke halaman `/login`.

### [x] 4. Perlindungan Sesi dan Kredensial
- [x] Menggunakan algoritma hashing modern (BCRYPT / Argon2) untuk penyimpanan password di database.
- [x] Mengaktifkan `SESSION_ENCRYPT` dan mengkonfigurasi `SESSION_DRIVER` secara aman di produksi.

### [ ] 5. Keamanan Lingkungan Produksi (Langkah Selanjutnya)
- [ ] Nonaktifkan mode debug di produksi (`APP_DEBUG=false` pada berkas `.env`).
- [ ] Gunakan sertifikat SSL/HTTPS (`FORCE_HTTPS=true`).
- [ ] Audit dependensi paket secara berkala dengan menjalankan perintah `composer audit` dan `npm audit`.
