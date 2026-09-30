<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;

echo "=== Articles with 'maradona' in title ===\n";
$articles = Article::where(function($q) {
    $q->where('title_fr', 'like', '%maradona%')
      ->orWhere('title_en', 'like', '%maradona%');
})->get();

if ($articles->isEmpty()) {
    echo "No articles found with 'maradona'\n";
} else {
    foreach ($articles as $a) {
        echo "ID: {$a->id} | FR: {$a->title_fr} | EN: {$a->title_en} | Published: {$a->is_published}\n";
    }
}

echo "\n=== All articles (first 10) ===\n";
$all = Article::take(10)->get();
foreach ($all as $a) {
    echo "ID: {$a->id} | FR: {$a->title_fr} | EN: {$a->title_en} | Published: {$a->is_published} | Lang: {$a->language}\n";
}

echo "\n=== Total articles: " . Article::count() . "\n";