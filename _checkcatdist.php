<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use Illuminate\Support\Str;

$articles = Article::where('source_type', 'imported')
    ->orderBy('id', 'desc')
    ->take(20)
    ->get();

foreach ($articles as $a) {
    $cat = $a->category ? $a->category->name_fr : 'NULL';
    echo "ID: {$a->id} | Cat: {$cat} | Lang: {$a->language} | Source: {$a->source_name} | Title: " . Str::limit($a->title_fr, 50) . PHP_EOL;
}

echo "\nCategory distribution:\n";
$dist = Article::where('source_type', 'imported')
    ->join('categories', 'articles.category_id', '=', 'categories.id')
    ->selectRaw('categories.name_fr, count(*) as count')
    ->groupBy('categories.name_fr')
    ->orderByDesc('count')
    ->get();

foreach ($dist as $d) {
    echo "{$d->name_fr}: {$d->count}\n";
}