<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Decode the session cookie to get the session ID
$cookie = 'eyJpdiI6IkIvcEhwY0Qvb09KbUljMWlkVGlWUGc9PSIsInZhbHVlIjoiSW5lYnFScE9wR3J0UUxZL0lUemZBOGVFMDhwNDU0YnVZbzNxVm9aVCt3czFiMGIwUHdHY29iYkFHajRpeDJQWGtOeEpvOFZuN1dqdFN4U3dOTkRLYzdLdTVBbDNWbXFvRU5NcXg5QkRFZElzVENZVDZNY0l1T1l6SGQxUlRuT2YiLCJtYWMiOiIwNDc3OGRjOGVhN2ZhZWExYzNiNDNmZTljZWM3Y2U1NjVkYTAzNWZkNDQ4NGY3YmFmYTNkZjY4ZDJiOTkyOWM0IiwidGFnIjoiIn0%3D';
$decoded = json_decode(base64_decode(urldecode($cookie)), true);
echo "Session ID from cookie: " . $decoded['value'] . PHP_EOL;

$session = DB::table('sessions')->where('id', $decoded['value'])->first();
if ($session) {
    echo "Session in DB - User ID: " . ($session->user_id ?? 'null') . PHP_EOL;
} else {
    echo "Session NOT in DB!" . PHP_EOL;
}