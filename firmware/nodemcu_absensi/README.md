# Firmware mesin absensi NodeMCU ESP8266

Firmware ini mengirim UID RFID ke endpoint aplikasi:

```text
GET /absensi1/webapi/api/create.php?uid=UID_KARTU&dev_eui=GATE-01
```

## Wiring

| NodeMCU ESP8266 | RC522 | LCD I2C 16x2 | Active buzzer |
|---|---|---|---|
| 3V3 | 3.3V | VCC* | VCC* |
| GND | GND | GND | GND |
| D0 / GPIO16 | RST | - | - |
| D1 / GPIO5 | - | SCL | - |
| D2 / GPIO4 | - | SDA | - |
| D3 / GPIO0 | SDA/SS | - | - |
| D5 / GPIO14 | SCK | - | - |
| D6 / GPIO12 | MISO | - | - |
| D7 / GPIO13 | MOSI | - | - |
| D8 / GPIO15 | - | - | Signal/IN |

\* RC522 wajib 3.3 V. Untuk LCD dan buzzer, ikuti spesifikasi modul. Bila
backpack LCD membutuhkan 5 V, gunakan level shifter I2C agar pin ESP8266 tidak
tertarik ke 5 V. Active buzzer harus bertipe 3.3 V atau dikendalikan melalui
transistor.

RC522 memakai label `SDA`, tetapi pada mode SPI pin tersebut sebenarnya adalah
pin `SS/CS`; jangan sambungkan pin itu ke SDA LCD.

## Library Arduino IDE

Pasang library berikut melalui Library Manager:

- WiFiManager by tzapu
- MFRC522 by GithubCommunity
- LiquidCrystal I2C by Frank de Brabander
- ArduinoJson by Benoit Blanchon
- Board package `esp8266 by ESP8266 Community` versi 3.x

Pilih board `NodeMCU 1.0 (ESP-12E Module)` dan baud upload yang stabil, misalnya
115200.

## Konfigurasi pertama

1. Upload `nodemcu_absensi.ino`.
2. NodeMCU membuat hotspot `ABSENSI-SETUP` dengan password `absensi123` jika
   belum mempunyai koneksi WiFi.
3. Sambungkan ponsel ke hotspot tersebut lalu buka portal yang muncul. Jika
   tidak muncul otomatis, buka `http://192.168.4.1`.
4. Pilih WiFi sekolah, isi password, URL API lengkap, dan ID mesin.
5. Contoh URL untuk server Laragon pada IP `192.168.1.10`:

   ```text
   http://192.168.1.10/absensi1/webapi/api/create.php
   ```

   Jangan memakai `localhost`, karena dari NodeMCU kata tersebut menunjuk ke
   NodeMCU sendiri, bukan komputer Laragon.

6. Nilai URL API dan ID mesin disimpan di LittleFS.

Untuk membuka portal dari awal lagi, hapus flash/konfigurasi WiFi dari Arduino
IDE (`Erase Flash: All Flash Contents`) lalu upload ulang.

## Catatan LCD

Alamat backpack LCD yang umum adalah `0x27`. Jika layar menyala tetapi tidak
menampilkan tulisan, jalankan I2C scanner atau ubah `LCD_ADDRESS` di sketch
menjadi `0x3F`, lalu atur trimpot kontras pada backpack LCD.

## Format UID

UID dikirim sebagai HEX huruf besar, dua digit per byte, tanpa spasi. Contoh:
`04A1B20F`. Pastikan UID yang tersimpan pada data siswa/guru memakai format yang
sama.
