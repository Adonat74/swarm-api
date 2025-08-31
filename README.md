# SWARM

## procedures to install 

- fill the .env file
- `docker compose up`
- once in the apache-php container run `php artisan migrate`
- composer require php-open-source-saver/jwt-auth
- php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
- php artisan jwt:secret
