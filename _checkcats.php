<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

$categories = Category::all();
foreach ($categories as $c) {
    echo "ID: {$c->id} | name_fr: [{$c->name_fr}] | name_en: [{$c->name_en}] | is_active: " . ($c->is_active ?? 'null') . PHP_EOL;
}