<?php
$env = file_get_contents('C:/sununews/.env');
$env = str_replace('DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql', $env);
$env = str_replace('# DB_HOST=127.0.0.1', 'DB_HOST=localhost', $env);
$env = str_replace('# DB_PORT=3306', 'DB_PORT=3306', $env);
$env = str_replace('# DB_DATABASE=laravel', 'DB_DATABASE=sununews', $env);
$env = str_replace('# DB_USERNAME=root', 'DB_USERNAME=root', $env);
$env = str_replace('# DB_PASSWORD=', 'DB_PASSWORD=', $env);
$env = str_replace('APP_URL=http://localhost:8000', 'APP_URL=http://localhost:3002', $env);
$env = str_replace('APP_LOCALE=en', 'APP_LOCALE=fr', $env);
$env = str_replace('APP_FALLBACK_LOCALE=en', 'APP_FALLBACK_LOCALE=fr', $env);
$env = str_replace('APP_MAINTENANCE_DRIVER=file', 'APP_MAINTENANCE_DRIVER=file', $env);
$env = str_replace('QUEUE_CONNECTION=database', 'QUEUE_CONNECTION=database', $env);
file_put_contents('C:/sununews/.env', $env);
echo "CONFIGURED\n";
