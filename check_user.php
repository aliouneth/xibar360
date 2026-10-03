<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'admin@example.com')->first();
echo 'User: ' . $u->name . ' ID: ' . $u->id . ' Roles: ';
foreach($u->roles as $r) echo $r->name . ' ';
echo "\n";