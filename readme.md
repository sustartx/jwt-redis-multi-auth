### Description

Geliştirdiğim bir Laravel projesinde oturum ve yetkilendirme işlemlerini JWT ile yapmam gerekiyordu. Projelerimde genellikle tercih ettiğim `tymon/jwt-auth` paketinin eskimiş olmasından ve yine sıkça tercih ettiğim `spatie/laravel-permission` paketini de kullanmam gerektiğinden ortak paket arayışına girerek [Samet Şahin Doğan](https://github.com/sametsahindogan)'ın geliştirdiği `sametsahindogan/laravel-jwtredis` paketine ulaştım. Bu paketin de kendi içindeki ufak bağımlılık sorunlarından dolayı tamamen kendi ihtiyaçlarıma uygun değişiklikler yaparak paketi farklı şekilde baştan yazmaya çalıştım. JWT paketini `php-open-source-saver/jwt-auth` ile değiştirip sorunları giderdim.

`User` modeli haricinde `JWTRedisMultiAuthAuthenticatableBaseModel` sınıfından extend edilmiş tüm modeller ile oturum yönetimi yapılabilir.

Geliştirdiği `laravel-jwtredis` paketi için Samet Şahin Doğan'a teşekkürler.

**En kısa sürede paketin kullanımına dair detaylı bir içerik hazırlayıp örnek bir proje ekleyeceğim.**

### TODO
- [x] Farklı modeller ile giriş yapılabilmeli
- [x] İstenilen modele özel JWT üretilebilmeli
- [x] Gelen request ile hangi Guard, Provider ve Model ile işleneceği tespit edilebilmeli
- [x] Giriş sırasında yetkiler alınabilmeli
- [x] Redis'e bilgiler kaydedilebilmeli
- [x] Observer ile modeldeki herhangi bir değişiklikte Redis verisi güncellenmeli
- [x] Middleware işlemleri tamamlanmalı
- [x] Login sırasında ilgili model otomatik tespit edilerek redis key kısmında $model_adi + $model_id şeklinde key belirlenmeli
- [x] Kullanıcı güncellendiğinde Redis verisi güncellenmeli
- [x] Refreshable çalışmalı
- [x] Yetkiler (relation işlemleri) güncellendiğinde Redis verisi güncellenmeli
- [x] Response yapısı güncellenmeli
- [x] Hem Cookie hem Authorization header bilgisi ile token kontrol edilebilmeli
- [x] Kullanıcıların hangi bilgilerinin rediste saklanacağı geliştiriciye bırakılmalı.
- [ ] Redis ayarlardan aktif veya pasif yapılabilmeli.
- [ ] Geçerli bir JWT ile request geldiyse ve kullanıcı Redis içinde yoksa JWT ile yeniden cache oluşturulmalı, cache öncesinde veritananında kullanıcının olup olmadığı 1 kere kontrol edilmeli
- [ ] Ban durumu ile ilgili altyapı güncellenmeli, banned_statuses işlemleri kontrol edilmeli

## License
MIT © [Şakir Mehmetoğlu](https://github.com/sustartx/jwt-redis-multi-auth/blob/master/LICENSE)
