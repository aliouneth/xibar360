<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate an HTTP request
$request = $app['request'];
$request->setMethod('POST');
$request->server->set('HTTP_HOST', 'localhost:3002');
$request->server->set('REQUEST_URI', '/login');
$request->request->set('email', 'admin@example.com');
$request->request->set('password', 'password');
$request->request->set('_token', 'test');

// Run the kernel
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle($request);

echo "Response status: " . $response->getStatusCode() . PHP_EOL;

// Check session
$session = $app['session'];
echo "Session ID: " . $session->getId() . PHP_EOL;

// Check session in DB
$dbSession = DB::table('sessions')->where('id', $session->getId())->first();
if ($dbSession) {
    echo "Session in DB - User ID: " . ($dbSession->user_id ?? 'null') . PHP_EOL;
    echo "Last activity: " . $dbSession->last_activity . PHP_EOL;
} else {
    echo "Session not found in DB!" . PHP_EOL;
}