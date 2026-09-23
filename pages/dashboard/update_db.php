<?php
session_start();

if (($_SESSION['akses'] ?? '') !== 'Admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

http_response_code(410);
echo 'Updater database melalui browser sudah dinonaktifkan. Jalankan: php scripts/migrate.php';
