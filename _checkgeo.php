<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PageView;

echo "=== Page views with country data ===\n";
$total = PageView::count();
$withCountry = PageView::whereNotNull('country')->count();
echo "Total: $total, With country: $withCountry\n\n";

$countries = PageView::whereNotNull('country')
    ->select('country', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
    ->groupBy('country')
    ->orderByDesc('views')
    ->take(10)
    ->get();

echo "Top countries:\n";
foreach ($countries as $c) {
    echo "  {$c->country}: {$c->views} views, {$c->visitors} visitors\n";
}