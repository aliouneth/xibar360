<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'admin@example.com')->first();
echo 'Password hash: ' . $u->password . PHP_EOL;
echo 'Check password: ' . (password_verify('password', $u->password) ? 'MATCH' : 'NO MATCH') . PHP_EOL;