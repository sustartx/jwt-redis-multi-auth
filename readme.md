### Description

Geliştirdiğim bir Laravel projesinde oturum ve yetkilendirme işlemlerini JWT ile yapmam gerekiyordu. Projelerimde genellikle tercih ettiğim `tymon/jwt-auth` paketinin eskimiş olmasından ve yine sıkça tercih ettiğim `spatie/laravel-permission` paketini de kullanmam gerektiğinden ortak paket arayışına girerek Samet Şahin Doğan'ın geliştirdiği `sametsahindogan/laravel-jwtredis` paketine ulaştım. Bu paketin de kendi içindeki ufak bağımlılık sorunlarından dolayı tamamen kendi ihtiyaçlarıma uygun değişiklikler yaparak paketi farklı şekilde baştan yazmaya çalıştım. JWT paketini `php-open-source-saver/jwt-auth` ile değiştirip sorunları giderdim. 

`User` modeli haricinde `JWTRedisMultiAuthAuthenticatableBaseModel` sınıfından extend edilmiş tüm modeller ile oturum yönetimi yapılabilir.

Geliştirdiği `laravel-jwtredis` paketi için Samet Şahin Doğan'a teşekkürler.

**En kısa sürede paketin kullanımına dair detaylı bir içerik hazırlayıp örnek bir proje ekleyeceğim.** 

### Installation
```bash
composer require sustartx/jwt-redis-multi-auth predis/predis
```
`.env` içinde aşağıdaki değişiklikleri yapın.
```dotenv
CACHE_DRIVER=redis
REDIS_CLIENT=predis
```
`guards` ve `providers` içeriğini `config/auth.php` dosyasından aşağıdaki gibi değiştirin.
```php
'guards' => [
        'api' => [
            'driver' => 'jwt_redis_guard',
            'provider' => 'users'
        ],
    ],

'providers' => [
        'users' => [
            'driver' => 'jwt_redis_user_provider',
            'model' =>  App\Models\User::class,
        ],
    ],
```
Laravel `auto-discovery` ile paketi otomatik bulup kayıt edecektir. Eğer kendiniz kayıt etmek istiyorsanız `config/app.php` içindeki `providers` dizisine aşağıdaki satır ekleyin.
```php
SuStartX\JWTRedisMultiAuth\JWTRedisServiceProvider::class,
```

#### Publish
```bash
php artisan vendor:publish --provider="SuStartX\JWTRedisMultiAuth\JWTRedisMultiAuthServiceProvider" --tag="config"
```

### TODO 
- Redis ayarlardan aktif veya pasif yapılabilmeli.
- Kullanıcı giriş yapınca ayar durumuna göre bilgisi rediste saklanmalı.
- Kullanıcıların hangi bilgilerinin rediste saklanacağı geliştiriciye bırakılmalı.


## License
MIT © [Şakir Mehmetoğlu](https://github.com/sustartx/jwt-redis-multi-auth/blob/master/LICENSE)
