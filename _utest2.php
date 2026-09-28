<?php

$base = 'http://localhost:3002';
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\utest2.txt';
@unlink($jar);

function req(string $url, ?array $post = null): array
{
    global $jar;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
    ]);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $b = curl_exec($ch);
    $c = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $l = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    return ['code' => $c, 'body' => $b, 'loc' => $l];
}

function csrf(string $h): string
{
    preg_match('/name="_token" value="([^"]+)"/', $h, $m);

    return $m[1] ?? '';
}

req("$base/login");
$r = req("$base/login", ['_token' => csrf(req("$base/login")['body']), 'email' => 'admin@example.com', 'password' => 'password']);

// Submit an invalid user and follow the redirect so the flash renders.
$r = req("$base/admin/users/create");
$r = req("$base/admin/users", [
    '_token' => csrf($r['body']),
    'name' => '',
    'email' => 'not-an-email',
    'password' => 'short',
    'password_confirmation' => 'mismatch',
]);
echo "POST status: {$r['code']} -> {$r['loc']}\n";
$r = req($r['loc']);
echo "GET  status: {$r['code']}\n";

echo "\nerror summary block present: "
    .(str_contains($r['body'], 'Please fix the following') ? 'YES' : 'NO')."\n";
echo "invalid-feedback fields: "
    .preg_match_all('/is-invalid/', $r['body'])."\n";
echo "\nrendered error messages:\n";
preg_match_all('/<li>([^<]{5,160})<\/li>/', $r['body'], $m);
foreach (array_slice($m[1], 0, 8) as $msg) {
    echo "  - ".trim(html_entity_decode($msg))."\n";
}
