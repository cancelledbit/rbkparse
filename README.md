
# Descriprion
This is simple RBK newsfeed parser written with Laravel for test purposes.
# How to deploy (if you whant it for some reason)
## Requirements
* Linux server (debian based if preferred)
* PHP >= 7.3 with all extensions nesseary for laravel
* * BCMath PHP Extension
* * Ctype PHP Extension
* * Fileinfo PHP Extension
* * JSON PHP Extension
* * Mbstring PHP Extension
* * OpenSSL PHP Extension
* * PDO PHP Extension
* * Tokenizer PHP Extension
* * XML PHP Extension
* SQLite
* nginx

## Step 1. 
Clone this repository to your web server and install dependencies, i assume it would be /var/www
* `$ git clone https://github.com/cancelledbit/rbkparse.git && cd ./rbkparse`
* Install composer as described here https://getcomposer.org/download/
* `$ ./composer.phar install `
* `$ touch .env && nano .env`
* Paste this to .env and save
```
APP_NAME=Parser
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/rbkparse/news.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```
* add following to your crontab ```* * * * * cd /var/www/rbkparse && php artisan schedule:run >> /dev/null 2>&1``
* `$ php artisan serve --host=0.0.0.0 --port 8081`
Now you can access service with your browser http://<YOUR-EXTERAL-IP>:8081/
