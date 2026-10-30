<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Redis Cache Configuration
| -------------------------------------------------------------------------
|
| Konfigurasi ini digunakan oleh CI3 Redis Cache driver.
| Secara default berjalan di lokal tanpa password (cocok untuk Laragon/Hostinger Default).
| Jika Hostinger memberikan password khusus untuk Redis, tambahkan di sini.
|
*/

$config['socket_type'] = 'tcp';
$config['host']        = '127.0.0.1';
$config['password']    = NULL; // Isi dengan password Redis jika diperlukan oleh hosting
$config['port']        = 6379;
$config['timeout']     = 0;
