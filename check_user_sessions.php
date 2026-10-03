<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$s = DB::table('sessions')->where('user_id', 1)->get();
foreach($s as $r) echo 'ID: ' . $r->id . ' User: ' . ($r->user_id ?? 'null') . PHP_EOL;