# Dızo Wear - Kurulum Rehberi

Bu rehberde scripti sıfırdan nasıl kuracağınızı anlatıyorum. Adım adım takip ederseniz 5-10 dakikada hazır olur.

---

## Başlamadan Önce

Şu şeylere ihtiyacınız var:

### Localhost için (bilgisayarınızda test etmek için):
- XAMPP, Laragon veya benzeri bir yerel sunucu
- PHP 8.0 veya üzeri
- MySQL 5.7 veya üzeri

### Canlı hosting için:
- PHP 8.0+ destekleyen bir hosting
- MySQL veritabanı
- SSL sertifikası (https için - zorunlu değil ama önerilir)

### PHP Uzantıları (genelde zaten yüklü olur):
- PDO
- pdo_mysql
- curl
- json
- mbstring

---

## Yöntem 1: Otomatik Kurulum (Önerilen)

Bu en kolay yöntem. 3-4 tıklamayla bitiyor.

### Adım 1: Dosyaları Yükleyin

**Localhost için:**
1. ZIP dosyasını indirin
2. `htdocs` klasörüne çıkarın (XAMPP) veya `www` klasörüne (Laragon)
3. Klasör adı `dizowear` olsun

**Canlı hosting için:**
1. ZIP dosyasını indirin
2. Hosting panelinizden (cPanel, Plesk vs.) dosya yöneticisine girin
3. `public_html` klasörüne yükleyin ve çıkarın

### Adım 2: Kurulum Sayfasını Açın

Tarayıcınızda şu adrese gidin:

```
Localhost: http://localhost/dizowear/install.php
Hosting:   http://siteniz.com/install.php
```

### Adım 3: Veritabanı Bilgilerini Girin

Kurulum ekranında şunları doldurun:

| Alan | Localhost İçin | Hosting İçin |
|------|---------------|--------------|
| Sunucu | localhost | localhost (genelde) |
| Veritabanı Adı | dizowear | hosting'ten öğrenin |
| Kullanıcı Adı | root | hosting'ten öğrenin |
| Şifre | (boş bırakın) | hosting'ten öğrenin |

**Not:** Hosting'de önce veritabanı oluşturmanız gerekebilir. cPanel kullanıyorsanız "MySQL Databases" bölümünden yapabilirsiniz.

### Adım 4: Admin Hesabı Oluşturun

- Adınızı girin
- E-posta adresinizi girin
- Güçlü bir şifre belirleyin

### Adım 5: Kurulumu Tamamlayın

"Kurulumu Başlat" butonuna tıklayın. Birkaç saniye içinde:
- Veritabanı tabloları oluşturulacak
- Ayarlar kaydedilecek
- Admin hesabınız açılacak

**Önemli:** Kurulum bittikten sonra `install.php` ve `install_schema.sql` dosyalarını silin! Güvenlik için önemli.

---

## Yöntem 2: Manuel Kurulum

Otomatik kurulum çalışmazsa veya daha fazla kontrol istiyorsanız bu yöntemi kullanın.

### Adım 1: Veritabanı Oluşturun

phpMyAdmin veya MySQL komut satırından:

```sql
CREATE DATABASE dizowear CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Adım 2: Tabloları Oluşturun

`install_schema.sql` dosyasını phpMyAdmin'e import edin:

1. phpMyAdmin'i açın
2. Sol taraftan `dizowear` veritabanını seçin
3. Üstten "İçe Aktar" (Import) sekmesine tıklayın
4. `install_schema.sql` dosyasını seçin
5. "Git" (Go) butonuna tıklayın

### Adım 3: Veritabanı Ayarlarını Yapın

`config/database.php` dosyasını açın ve şu satırları düzenleyin:

```php
private $host = 'localhost';
private $dbname = 'dizowear';
private $username = 'root';
private $password = '';
```

### Adım 4: Admin Hesabı Oluşturun

Önce şifrenizi hashleyin. PHP'de şöyle yapabilirsiniz:

```php
<?php
echo password_hash('sizin_sifreniz', PASSWORD_BCRYPT);
```

Sonra bu SQL'i çalıştırın:

```sql
INSERT INTO users (name, email, password, role, status) VALUES 
('Admin', 'admin@siteniz.com', 'HASH_BURADA', 'admin', 'active');
```

---

## İlk Giriş

Kurulum bittikten sonra:

**Admin Panel:** `http://siteniz.com/admin/`
**Site:** `http://siteniz.com/`

Admin panele kurulumda belirlediğiniz e-posta ve şifre ile giriş yapın.

---

## İlk Yapılacaklar

### 1. Site Ayarları
Admin Panel → Ayarlar → Site Ayarları
- Site adını değiştirin
- Telefon ve e-posta ekleyin
- Sosyal medya linklerini girin

### 2. Ödeme Ayarları
Admin Panel → Ödeme Ayarları
- PayTR veya İyzico bilgilerinizi girin
- Test modunda kalabilirsiniz başlangıçta

### 3. Kategori Oluşturun
Admin Panel → Kategoriler
- En az bir kategori ekleyin

### 4. İlk Ürününüzü Ekleyin
Admin Panel → Ürünler → Yeni Ürün
- Ürün bilgilerini girin
- En az bir görsel yükleyin
- Beden ve stok ekleyin

### 5. Slider Ekleyin (Opsiyonel)
Admin Panel → Sliderlar
- Ana sayfa için slider görselleri ekleyin

---

## Sık Karşılaşılan Sorunlar

### Sayfa bulunamadı (404) hatası
`.htaccess` dosyası çalışmıyor olabilir. Hosting'inizde mod_rewrite aktif olmalı.

**Çözüm:** Hosting destek'e "mod_rewrite aktif mi?" diye sorun.

### Veritabanı bağlantı hatası
`config/database.php` dosyasındaki bilgiler yanlış olabilir.

**Çözüm:** Hosting panelinizden doğru bilgileri alın.

### Görsel yüklenmiyor
`uploads/` klasörüne yazma izni yok olabilir.

**Çözüm:** Klasör iznini 755 veya 775 yapın.

### CSS/JS yüklenmiyor
URL yapılandırması hatalı olabilir.

**Çözüm:** `config/app.php` dosyasındaki `BASE_URL`'i kontrol edin.

### Boş sayfa, hiçbir şey yok
PHP hatası var ama görünmüyor.

**Çözüm:** `error_reporting(E_ALL)` ve `ini_set('display_errors', 1)` ekleyip hatayı görün.

---

## Hosting Önerileri

Şu hosting'lerde test ettim, sorunsuz çalışıyor:

- **Turhost** - Fiyat/performans dengesi iyi
- **Natro** - Hızlı destek
- **GoDaddy** - Uluslararası seçenek
- **DigitalOcean** - VPS tercih edenler için

**Minimum gereksinimler:**
- 1 GB RAM
- 1 GB disk alanı
- PHP 8.0+
- MySQL 5.7+

---

## SSL (HTTPS) Kurulumu

Güvenlik için SSL şart. Çoğu hosting ücretsiz SSL veriyor (Let's Encrypt).

1. Hosting panelinizden SSL aktif edin
2. Site çalıştıktan sonra `config/app.php` dosyasında URL'i `https://` yapın
3. Bitti!

---

## Production Kontrol Listesi

Siteyi canlıya almadan önce:

- [ ] `install.php` ve `install_schema.sql` silindi mi?
- [ ] Ödeme test modu kapalı mı?
- [ ] Admin şifresi güçlü mü?
- [ ] SSL aktif mi?
- [ ] Hata gösterimi kapalı mı?
- [ ] Yedekleme planı var mı?

---

## Yedekleme

Düzenli yedek almayı unutmayın!

### Veritabanı Yedeği
phpMyAdmin → Export → Go

### Dosya Yedeği
FTP ile `uploads/` klasörünü indirin (ürün görselleri burada)

---

## Güncelleme

Yeni sürüm çıktığında:

1. Mevcut dosyalarınızı yedekleyin
2. `config/` ve `uploads/` klasörlerini saklayın
3. Yeni dosyaları yükleyin
4. Sakladığınız `config/` ve `uploads/` klasörlerini geri koyun

---

## Yardım

Sorun yaşarsanız:

1. Önce bu dokümana bakın
2. GitHub Issues'da arayın, belki aynı sorunu yaşayan var
3. Bulamazsanız yeni issue açın

Kolay gelsin! 🎉
