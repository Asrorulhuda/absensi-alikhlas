# Deployment produksi (PHP 8.3)

## Persyaratan hosting

- PHP 8.3
- MySQL 8+ atau MariaDB 10.3+
- Ekstensi PHP: `mysqli`, `pdo_mysql`, `curl`, `json`, `openssl`, `fileinfo`, dan `gd`
- Apache `mod_rewrite`
- Git, PHP CLI, dan fungsi PHP `exec()` untuk auto-deploy
- HTTPS aktif pada domain

Versi PHP diatur dari panel hosting/cPanel. Repository tidak memaksa handler
PHP tertentu agar tidak menyebabkan HTTP 500 pada hosting non-cPanel.

## Konfigurasi privat

Kedua file berikut tidak boleh masuk Git:

1. Salin `include/db_config.example.php` menjadi `include/db_config.php`, lalu
   isi kredensial database produksi.
2. Salin `include/production_config.example.php` menjadi
   `include/production_config.php`, lalu ganti seluruh secret contoh dengan
   nilai acak. Contoh membuat secret 64 karakter:

   ```bash
   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
   ```

File privat tersebut sudah tercantum dalam `.gitignore`, sehingga tidak akan
ditimpa oleh deployment Git.

Untuk membuka halaman registrasi kartu publik di produksi, gunakan URL pertama
kali dengan token:

```text
https://DOMAIN_ANDA/registrasi_kartu.php?token=REGISTRATION_TOKEN_ANDA
```

Token kemudian disimpan di sesi dan URL dibersihkan otomatis. Admin yang sudah
login juga dapat membuka halaman tersebut tanpa token.

Secret webhook lama yang pernah ditulis langsung dalam source harus dianggap
sudah diketahui publik. Buat secret baru sebelum produksi.

## Migration database

Jalankan sekali setelah deployment pertama:

```bash
php scripts/migrate.php
```

Migration tersimpan di `database/migrations/`. Runner mencatat migration yang
sudah berhasil pada tabel `schema_migrations`, sehingga aman dijalankan lagi.
Deployment webhook berikutnya akan menjalankan migration secara otomatis.

Migration rilis ini menambahkan `wa_logs.guru_nama`, memastikan tabel antrean
WhatsApp tersedia, dan memastikan baris konfigurasi notifikasi ID 1 tersedia.
Migration tidak menghapus data.

## Webhook auto-deploy GitHub

Gunakan satu webhook berikut:

```text
https://DOMAIN_ANDA/webhook_deploy.php
```

Pengaturan GitHub:

- Content type: `application/json`
- Secret: harus sama dengan `webhook_secret` di `production_config.php`
- SSL verification: aktif
- Event: Just the push event
- Branch deployment default: `main`

Deployment menggunakan `git merge --ff-only`, bukan `git reset --hard`.
Apabila ada perubahan tracked langsung di server, deployment akan berhenti dan
mencatat error daripada menimpa perubahan tersebut. Hasil deployment tersimpan
di `deploy.log` dan file ini tidak masuk Git.

Pastikan nilai `php_cli` menunjuk executable PHP 8.3 CLI hosting, misalnya
`php`, `/usr/local/bin/php`, atau path yang diberikan penyedia hosting.

## API mesin absensi

Di produksi, isi `device_api_key` dengan secret acak. URL yang dimasukkan ke
WiFiManager NodeMCU menjadi:

```text
https://DOMAIN_ANDA/webapi/api/create.php?key=DEVICE_API_KEY_ANDA
```

Firmware otomatis menambahkan parameter `uid` dan `dev_eui`. Jika
`device_api_key` dikosongkan, API tetap terbuka untuk kompatibilitas pengujian
lokal; jangan gunakan mode terbuka pada produksi.

HTTPS pada firmware saat ini mengenkripsi koneksi tetapi memakai
`setInsecure()` agar sertifikat hosting yang diperbarui otomatis tidak memutus
mesin. API key melindungi endpoint dari request tanpa otorisasi, tetapi untuk
verifikasi identitas TLS penuh, root CA hosting perlu ditanam setelah domain dan
penerbit sertifikat final diketahui.

## Cron WhatsApp

Metode yang disarankan adalah PHP CLI:

```bash
php /PATH_APLIKASI/cron_wa_worker.php
```

Jika panel hosting hanya mendukung URL cron, gunakan HTTPS dengan token:

```text
https://DOMAIN_ANDA/cron_wa_worker.php?token=CRON_TOKEN_ANDA
```

Token harus sama dengan `cron_token` pada konfigurasi privat.

## Urutan rilis pertama

1. Backup database dan folder aplikasi hosting.
2. Push source ke branch `main`.
3. Pastikan webhook lama berhasil menarik commit ini.
4. Buat dua file konfigurasi privat di server dan ganti seluruh secret.
5. Jalankan `php scripts/migrate.php` sekali lewat terminal hosting.
6. Atur PHP 8.3 dan pastikan seluruh ekstensi tersedia.
7. Perbarui secret webhook GitHub dengan secret baru.
8. Uji login admin, pendaftaran kartu, satu tap masuk/pulang, dan cron WA.
9. Ubah URL API pada WiFiManager ke URL HTTPS produksi beserta API key.
10. Ganti password admin bawaan dan jangan gunakan `admin/admin` di produksi.

## Pemeriksaan sebelum push

```bash
php -l webhook_deploy.php
php scripts/migrate.php
git diff --check
```

Jangan ikut commit `include/db_config.php`, `include/production_config.php`,
file log, backup SQL berisi data, atau token asli.
