<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use Illuminate\Support\Facades\DB;

// Simulate the exact query
$query = Article::with(['category', 'author']);

$search = 'maradona';
$category = '';
$status = '';

if (!empty($category)) {
    $query->where('category_id', $category);
}

if (!empty($status)) {
    $query->where('is_published', $status);
}

if (!empty($search)) {
    $query->where(function ($q) use ($search) {
        $q->where('title_fr', 'like', "%{$search}%")
          ->orWhere('title_en', 'like', "%{$search}%");
    });
}

$articles = $query->latest()->paginate(15);

echo "Total: {$articles->total()}\n";
foreach ($articles as $a) {
    echo "ID: {$a->id} | FR: {$a->title_fr} | EN: {$a->title_en} | Published: {$a->is_published}\n";
}
echo "SQL: " . $articles->toSql() . "\n";