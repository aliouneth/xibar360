<?php
// Fix all controller imports and create views
// This script runs all the needed fixes

$base = 'C:/sununews/';

// Fix AdminController imports
$adminCtrl = file_get_contents($base . 'app/Http/Controllers/Admin/AdminController.php');
$adminCtrl = str_replace(
    "namespace App\Http\Controllers\Admin;\n\nuse App\Http\Controllers\Controller;\nuse App\Models\Article;",
    "namespace App\Http\Controllers\Admin;\n\nuse App\Http\Controllers\Controller;\nuse App\Models\Article;\nuse App\Models\Category;\nuse App\Models\Ad;\nuse App\Models\User;",
    $adminCtrl
);
file_put_contents($base . 'app/Http/Controllers/Admin/AdminController.php', $adminCtrl);

// Fix DashboardController imports
$dashCtrl = file_get_contents($base . 'app/Http/Controllers/Admin/DashboardController.php');
$dashCtrl = str_replace(
    "namespace App\Http\Controllers\Admin;\n\nuse App\Http\Controllers\Controller;\nuse App\Models\Article;",
    "namespace App\Http\Controllers\Admin;\n\nuse App\Http\Controllers\Controller;\nuse App\Models\Article;\nuse App\Models\Category;\nuse App\Models\Ad;\nuse App\Models\User;",
    $dashCtrl
);
file_put_contents($base . 'app/Http/Controllers/Admin/DashboardController.php', $dashCtrl);

echo "Controllers fixed\n";
