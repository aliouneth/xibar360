<?php
// Start the Laravel server
$command = 'C:\\wamp64\\bin\\php\\php8.3.28\\php.exe artisan serve --port=3002 --host=0.0.0.0';
echo "Starting server...\n";
// Use proc_open to start in background
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];
$process = proc_open($command, $descriptors, $pipes, 'C:\\sununews');
if (is_resource($process)) {
    echo "Server started with PID\n";
    proc_close($process);
}
echo "Done\n";
