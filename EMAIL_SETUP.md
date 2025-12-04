# Konfigurasi Email untuk Fitur Verifikasi

## Fitur yang Membutuhkan Email:
1. **Registrasi**: Verifikasi email setelah pendaftaran
2. **Perubahan Email Profil**: Verifikasi 2 langkah (OTP + link konfirmasi)

## Setup Email (Gmail)

### 1. Buka file `.env` di root project

### 2. Ubah konfigurasi MAIL sebagai berikut:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailanda@gmail.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=emailanda@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Generate App Password untuk Gmail:

#### Langkah-langkah:
1. Buka **Google Account** Anda: https://myaccount.google.com/
2. Pilih **Security** di sidebar kiri
3. Aktifkan **2-Step Verification** jika belum aktif
4. Setelah 2FA aktif, cari **App passwords**
5. Pilih **Mail** sebagai app dan **Other** sebagai device
6. Masukkan nama: "Laravel Ringkesin"
7. Google akan generate password 16 karakter
8. **Copy password tersebut** dan paste ke `MAIL_PASSWORD` di `.env`

⚠️ **PENTING**: 
- Jangan gunakan password Gmail biasa
- Gunakan App Password yang di-generate Google
- Format: `abcd efgh ijkl mnop` (16 karakter tanpa spasi)

### 4. Clear config cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## Testing Email

### Test menggunakan Tinker:

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Test email dari Ringkesin', function ($message) {
    $message->to('email_tujuan@gmail.com')
        ->subject('Test Email');
});
```

Jika muncul error, check:
- App Password sudah benar
- 2FA sudah aktif di Google Account
- File .env sudah di-load ulang (`php artisan config:clear`)

## Alternatif Email Service

Jika tidak ingin pakai Gmail, bisa gunakan:

### Mailtrap (Development):
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

### SendGrid:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
```

## Troubleshooting

### Error: "Connection could not be established"
- Pastikan firewall tidak memblokir port 587
- Coba ganti `MAIL_PORT` ke 465 dan `MAIL_ENCRYPTION` ke ssl

### Error: "Invalid credentials"
- Pastikan menggunakan App Password, bukan password Gmail biasa
- Pastikan 2FA sudah aktif di Google Account

### Email tidak sampai
- Cek folder Spam
- Pastikan `MAIL_FROM_ADDRESS` valid
- Test dengan Tinker terlebih dahulu

## Flow Verifikasi Email

### Registrasi:
1. User mengisi form register
2. Akun dibuat dengan `email_verified_at = null`
3. Email verifikasi dikirim dengan link valid 24 jam
4. User klik link → email terverifikasi → auto-login
5. Jika belum verifikasi, tidak bisa akses dashboard

### Perubahan Email:
1. User klik "Ubah Email" di profil
2. Masukkan email baru → sistem kirim OTP ke email lama
3. Masukkan OTP → sistem kirim link konfirmasi ke email baru
4. Klik link di email baru → email berhasil diubah

## File yang Terlibat

- `app/Http/Controllers/AuthController.php` - Register & verifikasi
- `app/Http/Controllers/ProfileController.php` - Perubahan email
- `app/Http/Middleware/EnsureEmailIsVerified.php` - Middleware verifikasi
- `routes/web.php` - Route verifikasi email
- `resources/views/auth/verify-email.blade.php` - Halaman notice
- `resources/views/profile/index.blade.php` - Modal perubahan email
