<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Installation

1. PHP 8, Composer, PHP extensions (Described on Laravel site), Python 3.8 is **required**
2. run your MySQL server
3. create ```.env``` file from ```.env.example```
4. set ```APP_URL``` variable to url of app
5. set your database connection information in the ```DB_CONNECTION```, ```DB_HOST```, ```DB_PORT```, ```DB_DATABASE```, ```DB_USERNAME```, ```DB_PASSWORD```
6. set ```BROADCAST_DRIVER``` variable to ```pusher```
7. set ```QUEUE_CONNECTION``` variable to ```database```
8. set ```PUSHER_APP_ID``` variable to whatever you want (recommended ```local```)
9. set ```PUSHER_APP_KEY``` variable to whatever you want (recommended ```local```)
10. set ```PUSHER_APP_SECRET``` variable to whatever you want (recommended ```local```)
11. set ```DEFAULT_ADMIN_USER_EMAIL``` to the email address of admin account (whatever you want)
12. set ```DEFAULT_ADMIN_USER_NAME``` to the username admin account (whatever you want)
13. set ```DEFAULT_ADMIN_USER_PASSWORD``` to the password of admin account (whatever you want)
14. set ```GOOGLE_CLIENT_ID``` to the value earned from console.cloud.google.com
15. set ```GOOGLE_CLIENT_SECRET``` to the value earned from console.cloud.google.com
16. set ```GOOGLE_LOGIN_REDIRECT``` to domain of frontend app
17. run ```composer install``` to install project dependencies
18. run ```php artisan key:generate``` to generate application key
19. run ```php artisan migrate``` to run database migrations
20. run ```php artisan passport:install``` to generate passport keys
21. second client id from previous command output copy to the ```PASSPORT_CLIENT_ID```
22. second secret key from previous command output copy to the ```PASSPORT_CLIENT_SECRET``` variable in ```.env``` file
23. set nginx vhost root to public directory of the project
24. in dev enviroment:
    1. run ```php artisan schedule:work``` to run scheduler
    2. run ```php artisan queue:work``` to run queue worker for queue experiments
25. in production enviroment:
    1. open crontab file with ```crontab -e``` and paste this:
    ```
    * * * * * cd {cesta} && php artisan schedule:run » /dev/null 2>&1
    ```
    2. install Supervisor on the server
    3. in ```/etc/supervisor/conf.d/``` directory create ```olm-worker.conf``` file and paste this:
    ```
    [program:olm-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php {cesta}/artisan queue:work --sleep=3 --tries=3 --max-time=3600 
    autostart=true 
    startsecs=0
    autorestart=true
    stopasgroup=true
    killasgroup=true user={pouzivatel_operacneho_systemu} 
    numprocs=8
    redirect_stderr=true 
    stdout_logfile={cesta}/worker.log 
    stopwaitsecs=3600
    ```
    4. run ```sudo supervisorctl reread```, ```sudo supervisorctl update``` and ```sudo supervisorctl
       start all```
