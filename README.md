# SWARM

## procedures to install 

- fill the .env file
- `docker compose up`
- - composer require php-open-source-saver/jwt-auth
- php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
- php artisan jwt:secret
- once in the apache-php container run `php artisan migrate:fresh --seed`
- create storage link for the images `php artisan storage:link`

