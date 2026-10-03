<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if Guard is bound
echo "Guard bound: " . ($app->bound(\Illuminate\Contracts\Auth\Guard::class) ? 'YES' : 'NO') . PHP_EOL;
echo "auth bound: " . ($app->bound('auth') ? 'YES' : 'NO') . PHP_EOL;

// Check what the auth manager returns
$auth = $app->make('auth');
echo "Default guard: " . $auth->getDefaultDriver() . PHP_EOL;
$guard = $auth->guard();
echo "Guard class: " . get_class($guard) . PHP_EOL;
echo "Guard ID: " . ($guard->id() ?? 'null') . PHP_EOL;

// Check the session handler
$session = $app['session'];
$handler = $session->getHandler();
echo "Session handler class: " . get_class($handler) . PHP_EOL;

// Check if handler's container has Guard bound
if (method_exists($handler, 'getContainer')) {
    $container = $handler->getContainer();
    echo "Handler container has Guard: " . ($container->bound(\Illuminate\Contracts\Auth\Guard::class) ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo "Handler doesn't have getContainer method" . PHP_EOL;
}