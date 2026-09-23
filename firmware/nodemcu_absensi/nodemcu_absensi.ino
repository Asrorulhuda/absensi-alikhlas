/*
 * Mesin Absensi NodeMCU ESP8266 12121
 * Perangkat: MFRC522, LCD I2C 16x2, active buzzer, WiFiManager
 * Backend  : webapi/api/create.php?uid=...&dev_eui=...
 *
 * Library yang diperlukan:
 * - ESP8266 board package 3.x
 * - WiFiManager by tzapu
 * - MFRC522 by GithubCommunity
 * - LiquidCrystal I2C by Frank de Brabander
 * - ArduinoJson by Benoit Blanchon (6.x atau 7.x)
 */

#include <Arduino.h>
#include <ArduinoJson.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <LiquidCrystal_I2C.h>
#include <LittleFS.h>
#include <MFRC522.h>
#include <SPI.h>
#include <WiFiClientSecureBearSSL.h>
#include <WiFiManager.h>
#include <Wire.h>

// ------------------------------ Pin NodeMCU ------------------------------
// RC522: SDA/SS=D3, SCK=D5, MISO=D6, MOSI=D7, RST=D0
// LCD  : SDA=D2, SCL=D1
// Buzzer aktif 3.3 V: signal=D8
constexpr uint8_t RFID_SS_PIN = D3;
constexpr uint8_t RFID_RST_PIN = D0;
constexpr uint8_t BUZZER_PIN = D8;
constexpr uint8_t LCD_SDA_PIN = D2;
constexpr uint8_t LCD_SCL_PIN = D1;

constexpr uint8_t LCD_ADDRESS = 0x27; // Ubah ke 0x3F jika LCD tidak tampil
constexpr uint8_t LCD_COLUMNS = 16;
constexpr uint8_t LCD_ROWS = 2;

constexpr char CONFIG_FILE[] = "/absensi-config.json";
constexpr char PORTAL_NAME[] = "ABSENSI-SETUP";
constexpr char PORTAL_PASSWORD[] = "absensi123";

// Nilai ini tampil sebagai nilai awal di portal WiFiManager.
// Ganti IP dengan IP komputer/server Laragon bila diperlukan.
char apiUrl[181] = "http://192.168.18.185/absensi1/webapi/api/create.php";
char deviceId[33] = "GATE-01";

MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLUMNS, LCD_ROWS);

bool shouldSaveConfig = false;
String lastUid;
unsigned long lastTapAt = 0;

void requestConfigSave() { shouldSaveConfig = true; }

void copyText(char *destination, size_t destinationSize, const char *source) {
  if (destinationSize == 0)
    return;
  strncpy(destination, source == nullptr ? "" : source, destinationSize - 1);
  destination[destinationSize - 1] = '\0';
}

String lcdText(String value) {
  value.replace("\r", " ");
  value.replace("\n", " ");
  if (value.length() > LCD_COLUMNS)
    value = value.substring(0, LCD_COLUMNS);
  while (value.length() < LCD_COLUMNS)
    value += ' ';
  return value;
}

void showMessage(const String &line1, const String &line2 = "") {
  lcd.setCursor(0, 0);
  lcd.print(lcdText(line1));
  lcd.setCursor(0, 1);
  lcd.print(lcdText(line2));
}

void beep(uint8_t count, uint16_t onTime = 100, uint16_t offTime = 100) {
  for (uint8_t i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(onTime);
    digitalWrite(BUZZER_PIN, LOW);
    if (i + 1 < count)
      delay(offTime);
  }
}

bool loadConfig() {
  if (!LittleFS.begin())
    return false;
  if (!LittleFS.exists(CONFIG_FILE))
    return false;

  File file = LittleFS.open(CONFIG_FILE, "r");
  if (!file)
    return false;

#if ARDUINOJSON_VERSION_MAJOR >= 7
  JsonDocument document;
#else
  StaticJsonDocument<512> document;
#endif
  DeserializationError error = deserializeJson(document, file);
  file.close();
  if (error)
    return false;

  copyText(apiUrl, sizeof(apiUrl), document["api_url"] | apiUrl);
  copyText(deviceId, sizeof(deviceId), document["device_id"] | deviceId);
  return true;
}

bool saveConfig() {
  if (!LittleFS.begin())
    return false;

#if ARDUINOJSON_VERSION_MAJOR >= 7
  JsonDocument document;
#else
  StaticJsonDocument<512> document;
#endif
  document["api_url"] = apiUrl;
  document["device_id"] = deviceId;

  File file = LittleFS.open(CONFIG_FILE, "w");
  if (!file)
    return false;
  bool success = serializeJson(document, file) > 0;
  file.close();
  return success;
}

String urlEncode(const String &value) {
  const char hex[] = "0123456789ABCDEF";
  String encoded;
  encoded.reserve(value.length() * 3);

  for (size_t i = 0; i < value.length(); i++) {
    uint8_t c = static_cast<uint8_t>(value[i]);
    if ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z') ||
        (c >= '0' && c <= '9') || c == '-' || c == '_' || c == '.' ||
        c == '~') {
      encoded += static_cast<char>(c);
    } else {
      encoded += '%';
      encoded += hex[(c >> 4) & 0x0F];
      encoded += hex[c & 0x0F];
    }
  }
  return encoded;
}

String readUid() {
  String uid;
  uid.reserve(rfid.uid.size * 2);
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10)
      uid += '0';
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}

void showReady() { showMessage("Tempelkan kartu", WiFi.localIP().toString()); }

void handleApiResponse(int httpCode, const String &payload) {
  Serial.printf("HTTP %d: %s\n", httpCode, payload.c_str());

  if (httpCode != HTTP_CODE_OK) {
    showMessage("Kartu ditolak",
                httpCode > 0 ? "Data/jadwal cek" : "Server offline");
    beep(3, 80, 80);
    return;
  }

#if ARDUINOJSON_VERSION_MAJOR >= 7
  JsonDocument document;
#else
  StaticJsonDocument<1024> document;
#endif
  DeserializationError error = deserializeJson(document, payload);
  if (error || !document.is<JsonObject>()) {
    showMessage("Respon API salah", "Cek server");
    beep(3, 80, 80);
    return;
  }

  String nama = document["nama"] | "Berhasil";
  String status = document["status"] | "OK";
  String waktu = document["waktu"] | "";

  // Respons API waktu umumnya HH:MM:SS. Jika berupa tanggal+waktu,
  // tampilkan delapan karakter terakhir saja.
  if (waktu.length() > 8)
    waktu = waktu.substring(waktu.length() - 8);
  String secondLine = status;
  if (waktu.length() > 0)
    secondLine += " " + waktu;

  showMessage(nama, secondLine);
  beep(1, 180, 0);
}

void sendAttendance(const String &uid) {
  if (WiFi.status() != WL_CONNECTED) {
    showMessage("WiFi terputus", "Menyambungkan...");
    beep(3, 80, 80);
    WiFi.reconnect();
    return;
  }

  String url = String(apiUrl);
  url += (url.indexOf('?') >= 0) ? '&' : '?';
  url += "uid=" + urlEncode(uid);
  url += "&dev_eui=" + urlEncode(String(deviceId));

  Serial.println("GET " + url);
  HTTPClient http;
  http.setTimeout(12000);
  http.setUserAgent("NodeMCU-Absensi/1.0");

  int httpCode = -1;
  String payload;

  if (url.startsWith("https://")) {
    BearSSL::WiFiClientSecure secureClient;
    // Praktis untuk sertifikat hosting yang dapat diperbarui. Untuk produksi
    // berkeamanan tinggi, ganti dengan trust anchor sertifikat server.
    secureClient.setInsecure();
    if (http.begin(secureClient, url)) {
      httpCode = http.GET();
      if (httpCode > 0)
        payload = http.getString();
      http.end();
    }
  } else {
    WiFiClient client;
    if (http.begin(client, url)) {
      httpCode = http.GET();
      if (httpCode > 0)
        payload = http.getString();
      http.end();
    }
  }

  handleApiResponse(httpCode, payload);
}

void connectWifi() {
  WiFi.mode(WIFI_STA);

  WiFiManager manager;
  manager.setSaveConfigCallback(requestConfigSave);
  manager.setConnectTimeout(20);
  manager.setConfigPortalTimeout(180);
  manager.setTitle("Konfigurasi Mesin Absensi");

  WiFiManagerParameter apiParameter("api_url", "URL API absensi", apiUrl,
                                    sizeof(apiUrl) - 1);
  WiFiManagerParameter deviceParameter("device_id", "ID mesin", deviceId,
                                       sizeof(deviceId) - 1);
  manager.addParameter(&apiParameter);
  manager.addParameter(&deviceParameter);

  showMessage("Menghubungkan", "WiFi...");
  bool connected = manager.autoConnect(PORTAL_NAME, PORTAL_PASSWORD);

  copyText(apiUrl, sizeof(apiUrl), apiParameter.getValue());
  copyText(deviceId, sizeof(deviceId), deviceParameter.getValue());
  if (shouldSaveConfig)
    saveConfig();

  if (!connected) {
    showMessage("WiFi gagal", "Restart alat");
    beep(3, 100, 100);
    delay(2500);
    ESP.restart();
  }
}

void setup() {
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW); // D8/GPIO15 harus tetap LOW saat boot

  Serial.begin(115200);
  Serial.println();
  Serial.println("Mesin Absensi ESP8266 mulai");

  Wire.begin(LCD_SDA_PIN, LCD_SCL_PIN);
  lcd.init();
  lcd.backlight();
  showMessage("MESIN ABSENSI", "Menyiapkan...");

  bool configLoaded = loadConfig();
  connectWifi();
  if (!configLoaded || shouldSaveConfig)
    saveConfig();

  SPI.begin();
  rfid.PCD_Init();
  delay(50);

  Serial.println("WiFi tersambung: " + WiFi.localIP().toString());
  Serial.println("API: " + String(apiUrl));
  Serial.println("Device: " + String(deviceId));

  beep(2, 70, 70);
  showReady();
}

void loop() {
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    delay(10);
    return;
  }

  String uid = readUid();
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  // Mencegah satu kartu yang masih menempel mengirim tap berulang kali.
  if (uid == lastUid && millis() - lastTapAt < 3000)
    return;
  lastUid = uid;
  lastTapAt = millis();

  Serial.println("UID: " + uid);
  showMessage("Memproses...", uid);
  beep(1, 50, 0);
  sendAttendance(uid);

  delay(2500);
  showReady();
}
