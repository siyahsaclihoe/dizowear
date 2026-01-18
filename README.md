# Dızo Wear - Ücretsiz E-Ticaret Scripti

Merhaba! 👋

Bu proje, giyim ve streetwear markaları için sıfırdan geliştirdiğim bir e-ticaret scripti. Tamamen ücretsiz olarak kullanabilirsiniz.

Uzun süredir PHP ile uğraşıyorum ve bu süreçte öğrendiklerimi bu projede topladım. Umarım sizin de işinize yarar.

---

## Bu Script Ne Yapıyor?

Kısaca söylemek gerekirse: online mağaza açmanızı sağlıyor. Ürünlerinizi eklersiniz, müşterileriniz sipariş verir, siz de kargoya verirsiniz. Basit.

Ama tabii altında bir sürü detay var:

- **Ürün yönetimi** - Birden fazla görsel, beden seçenekleri, stok takibi
- **Kategori sistemi** - İstediğiniz kadar kategori oluşturabilirsiniz
- **Sepet** - AJAX tabanlı, sayfa yenilenmeden çalışıyor
- **Ödeme** - PayTR ve İyzico entegrasyonu hazır (test modu var)
- **Kupon sistemi** - Yüzde veya sabit tutarda indirim
- **Admin panel** - Siparişler, müşteriler, istatistikler...

## Teknik Detaylar

Projeyi şu teknolojilerle geliştirdim:

- **PHP 8.0+** - Modern PHP, type hinting, arrow functions falan
- **MySQL** - Veritabanı için klasik MySQL
- **Bootstrap 5** - Responsive tasarım için
- **Vanilla JavaScript** - jQuery'e gerek kalmadı artık

Mimari olarak MVC benzeri bir yapı kullandım. `app/controllers`, `app/models`, `app/views` şeklinde. Tam kategorik bir framework değil ama işini görüyor.

## Kurulum

Detaylı kurulum için `KURULUM.md` dosyasına bakabilirsiniz. Ama özet geçeyim:

1. Dosyaları hostinginize yükleyin
2. `http://siteadresiniz.com/install.php` adresine gidin
3. Veritabanı bilgilerini girin, admin hesabı oluşturun
4. Bitti!

Localhost'ta da çalışıyor. XAMPP veya Laragon kurulu olması yeterli.

## Dosya Yapısı

```
dizowear/
├── admin/           → Admin panel dosyaları
├── app/
│   ├── controllers/ → Sayfa controller'ları
│   ├── models/      → Veritabanı sınıfları
│   ├── views/       → HTML template'leri
│   └── helpers/     → Yardımcı fonksiyonlar
├── assets/
│   ├── css/         → Stil dosyaları
│   └── js/          → JavaScript
├── config/          → Ayar dosyaları
├── uploads/         → Yüklenen görseller
└── public/          → Giriş noktası
```

## Özellikler (Detaylı)

### Tema Sistemi
Açık ve koyu tema desteği var. Kullanıcılar sağ alttaki butonla değiştirebiliyor. Tercih localStorage'da saklanıyor.

### Ödeme Sistemleri
Şu an PayTR ve İyzico entegre. Ayrıca geliştirme sırasında test etmek için "Demo Ödeme" modu da var. Gerçek para geçmiyor, sadece sipariş akışını test edebiliyorsunuz.

### Kupon Kodları
Admin panelden kupon oluşturabilirsiniz:
- Yüzde indirim (örn: %15)
- Sabit tutar (örn: 50 TL)
- Minimum sipariş tutarı şartı
- Kullanım limiti
- Başlangıç/bitiş tarihi

### Türkiye Adres Sistemi
Checkout sayfasında il seçince ilçeler otomatik geliyor. turkiyeapi.dev API'sını kullanıyorum, ücretsiz ve güvenilir.

### SEO
URL yapısı SEO dostu:
- `/urun-adi-slug` şeklinde ürün sayfaları
- `/kategori-slug` şeklinde kategoriler
- Meta description desteği

## Güvenlik

Güvenlik konusunda elimden geleni yaptım:

- SQL Injection'a karşı PDO prepared statements
- XSS'e karşı htmlspecialchars kullanımı
- CSRF token koruması
- Bcrypt ile şifre hashleme
- Dosya yüklemede MIME type kontrolü

Ama tabii %100 güvenli diye bir şey yok. Production'da mutlaka:
- SSL kullanın
- Hata mesajlarını kapatın
- config/ klasörüne erişimi engelleyin

## Bilinen Sorunlar

- Mahalle verisi şu an sabit, API'de yok
- Çoklu dil desteği henüz yok (gelecekte eklenebilir)
- Stok uyarı sistemi yok

## Katkıda Bulunma

Projeyi fork'layıp geliştirmelerinizi pull request olarak gönderebilirsiniz. Her türlü katkıya açığım.

Eğer bir bug bulursanız issue açabilirsiniz. Elimden geldiğince hızlı bakmaya çalışırım.

## Teşekkürler

Bu projeyi geliştirirken kullandığım açık kaynak araçlar:
- [Bootstrap](https://getbootstrap.com/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [Inter Font](https://fonts.google.com/specimen/Inter)
- [TurkeyAPI](https://turkiyeapi.dev/)

## Lisans

Bu proje **tamamen ücretsiz** ve açık kaynaklıdır. İstediğiniz gibi kullanabilir, değiştirebilir ve dağıtabilirsiniz.

Tek ricam: projeyi beğendiyseniz bir yıldız atmanız yeterli ⭐

---

**Sorularınız için:** GitHub Issues kullanabilirsiniz.

Kolay gelsin! 🚀
