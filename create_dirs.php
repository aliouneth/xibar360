<?php
// Create directory structure
$dirs = [
    'C:/sununews/app/Http/Controllers/Auth',
    'C:/sununews/app/Http/Controllers/Admin',
    'C:/sununews/app/Providers',
    'C:/sununews/lang/fr',
    'C:/sununews/lang/en',
    'C:/sununews/resources/views/layouts',
    'C:/sununews/resources/views/auth',
    'C:/sununews/resources/views/admin',
    'C:/sununews/resources/views/articles',
    'C:/sununews/resources/views/categories',
    'C:/sununews/resources/views/ads',
    'C:/sununews/resources/views/users',
    'C:/sununews/resources/views/search',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}
echo "Directories ready\n";
