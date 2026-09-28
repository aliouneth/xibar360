<?php

// End-to-end exercise of the admin user CRUD over real HTTP.
$base = 'http://localhost:3002';
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\utest.txt';
@unlink($jar);

function req(string $url, array $opts = []): array
{
    global $jar;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
    ]);
    if (isset($opts['post'])) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts['post']));
    }
    if (isset($opts['token'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-CSRF: '.$opts['token']]);
    }
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    return ['code' => $code, 'body' => $body, 'loc' => $loc];
}

function csrf(string $html): string
{
    preg_match('/name="_token" value="([^"]+)"/', $html, $m);

    return $m[1] ?? '';
}

function check(string $label, $got, $want)
{
    printf("  %-52s %-6s %s\n", $label, $got, $got == $want ? 'OK' : "FAIL (want $want)");
}

echo "=== login ===\n";
$r = req("$base/login");
$t = csrf($r['body']);
$r = req("$base/login", ['post' => ['_token' => $t, 'email' => 'admin@example.com', 'password' => 'password']]);
check('login redirects', $r['code'], 302);
$t = null;

echo "\n=== GET pages ===\n";
foreach ([
    '/admin/users' => 'users index',
    '/admin/users/create' => 'user create form',
    '/admin/users/1' => 'user show',
    '/admin/users/1/edit' => 'user edit form',
] as $url => $label) {
    $r = req("$base$url");
    check($label, $r['code'], 200);
}

echo "\n=== create user ===\n";
$r = req("$base/admin/users/create");
$t = csrf($r['body']);
$r = req("$base/admin/users", ['post' => [
    '_token' => $t,
    'name' => 'Test Editor',
    'email' => 'editor.test@example.com',
    'password' => 'secret1234',
    'password_confirmation' => 'secret1234',
    'roles' => [2],
]]);
check('store redirects to index', $r['code'], 302);
echo '  location: '.$r['loc']."\n";

echo "\n=== create with bad password (expect validation bounce) ===\n";
$r = req("$base/admin/users/create");
$t = csrf($r['body']);
$r = req("$base/admin/users", ['post' => [
    '_token' => $t,
    'name' => 'Bad',
    'email' => 'bad@example.com',
    'password' => 'short',
    'password_confirmation' => 'short',
]]);
check('redirects back with errors', $r['code'], 302);
$r2 = req("$base/admin/users/create");
check('error shown on form', str_contains($r2['body'], 'at least 8 characters') ? 'yes' : 'no', 'yes');
