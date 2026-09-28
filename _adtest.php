<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$base = 'http://localhost:3002';
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\adtest.txt';
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

function ck(string $label, $got, $want)
{
    printf("  %-40s %-5s %s\n", $label, $got, $got == $want ? 'OK' : "FAIL(want $want)");
}

// login
req("$base/login");
req("$base/login", ['_token' => csrf(req("$base/login")['body']), 'email' => 'admin@example.com', 'password' => 'password']);

// test ads pages
$r = req("$base/admin/ads");
ck('ads index', $r['code'], 200);
$r = req("$base/admin/ads/create");
ck('ads create form', $r['code'], 200);
$r = req("$base/admin/ads/1/edit");
ck('ads edit form', $r['code'], 200);

// test creating an ad
$r = req("$base/admin/ads/create");
$r = req("$base/admin/ads", [
    '_token' => csrf($r['body']),
    'title' => 'Test Ad Banner',
    'description' => 'Test description',
    'zone' => 'header',
    'target_url' => 'https://example.com',
    'order' => 1,
    'is_active' => '1',
]);
ck('create ad', $r['code'], 302);

// find the created ad
$adId = App\Models\Ad::where('title', 'Test Ad Banner')->value('id');
echo "  created ad id: $adId\n";

// test editing it
$r = req("$base/admin/ads/$adId/edit");
ck('edit created ad', $r['code'], 200);
$r = req("$base/admin/ads/$adId", [
    '_method' => 'PUT',
    '_token' => csrf($r['body']),
    'title' => 'Updated Ad Banner',
    'description' => 'Updated description',
    'zone' => 'sidebar',
    'target_url' => 'https://example.com/updated',
    'order' => 2,
    'is_active' => '1',
]);
ck('update ad', $r['code'], 302);
$ad = App\Models\Ad::find($adId);
ck('  title updated', $ad->title, 'Updated Ad Banner');
ck('  zone updated', $ad->zone, 'sidebar');

// cleanup
$ad->delete();
echo "  cleaned up test ad\n";

echo "\nDONE\n";
