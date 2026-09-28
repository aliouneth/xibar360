<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tr = app('translator');

echo 'locale          : '.$tr->getLocale()."\n";
echo 'langPath        : '.app()->langPath()."\n";
echo 'file exists     : '.(is_file(app()->langPath().'/en/validation.php') ? 'yes' : 'NO')."\n";
echo "validation.required -> ".$tr->get('validation.required')."\n";
echo "validation.min.string -> ".$tr->get('validation.min.string', ['min' => 8, 'attribute' => 'password'])."\n";

// Is a config/route cache holding an older lang path?
echo "\nbootstrap/cache contents:\n";
foreach (glob(__DIR__.'/bootstrap/cache/*') as $f) {
    echo '  '.basename($f)."\n";
}
