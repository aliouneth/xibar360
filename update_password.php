<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'admin@example.com')->first();
$u->password = bcrypt('password');
$u->save();
echo 'Password updated' . PHP_EOL;
echo 'Check: ' . (password_verify('password', $u->password) ? 'MATCH' : 'NO MATCH') . PHP_EOL;