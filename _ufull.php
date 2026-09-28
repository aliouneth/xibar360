<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function uid(string $email): ?int
{
    return App\Models\User::where('email', $email)->value('id');
}

$base = 'http://localhost:3002';
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\ufull.txt';
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
    printf("  %-48s %-5s %s\n", $label, $got, $got == $want ? 'OK' : "FAIL(want $want)");
}

$me = uid('admin@example.com');
$before = App\Models\User::count();

req("$base/login");
req("$base/login", ['_token' => csrf(req("$base/login")['body']), 'email' => 'admin@example.com', 'password' => 'password']);

// --- create ---
$r = req("$base/admin/users/create");
$r = req("$base/admin/users", [
    '_token' => csrf($r['body']),
    'name' => 'Awa Ndiaye', 'email' => 'awa@example.com',
    'password' => 'oldpass123', 'password_confirmation' => 'oldpass123',
    'roles' => [3],
]);
ck('create user', $r['code'], 302);
$id = uid('awa@example.com');
ck('row persisted', $id ? "id=$id" : 'none', "id=$id");
ck('password hashed (not plaintext)', Illuminate\Support\Facades\Hash::check('oldpass123', App\Models\User::find($id)->password) ? 'yes' : 'NO', 'yes');
ck('role attached', App\Models\User::find($id)->roles->pluck('name')->implode(',') === 'Viewer' ? 'Viewer' : App\Models\User::find($id)->roles->pluck('name')->implode(','), 'Viewer');
ck('is_active default', App\Models\User::find($id)->is_active ? '1' : '0', '1');

// --- edit name + role, password blank ---
$r = req("$base/admin/users/$id/edit");
$r = req("$base/admin/users/$id", [
    '_method' => 'PUT', '_token' => csrf($r['body']),
    'name' => 'Awa Ndiaye Diop', 'email' => 'awa@example.com',
    'password' => '', 'password_confirmation' => '',
    'roles' => [2],
]);
ck('edit user (password blank)', $r['code'], 302);
$u = App\Models\User::find($id);
ck('  name updated', $u->name, 'Awa Ndiaye Diop');
ck('  role changed to Editor', $u->roles->pluck('name')->implode(','), 'Editor');
ck('  password unchanged', Hash::check('oldpass123', $u->password) ? 'yes' : 'NO', 'yes');

// --- update password ---
$r = req("$base/admin/users/$id/edit");
$r = req("$base/admin/users/$id", [
    '_method' => 'PUT', '_token' => csrf($r['body']),
    'name' => 'Awa Ndiaye Diop', 'email' => 'awa@example.com',
    'password' => 'brandnew456', 'password_confirmation' => 'brandnew456',
    'roles' => [2],
]);
ck('update password', $r['code'], 302);
ck('  new hash matches', Hash::check('brandnew456', App\Models\User::find($id)->password) ? 'yes' : 'NO', 'yes');

// real login with the new password
$save = $jar;
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\uf_new.txt';
@unlink($jar);
req("$base/login");
$r = req("$base/login", ['_token' => csrf(req("$base/login")['body']), 'email' => 'awa@example.com', 'password' => 'brandnew456']);
ck('login with NEW password', str_contains($r['loc'], '/login') ? 'REJECTED' : 'accepted', 'accepted');
$jar = 'C:\Users\aliou\AppData\Local\Temp\opencode\uf_old.txt';
@unlink($jar);
req("$base/login");
$r = req("$base/login", ['_token' => csrf(req("$base/login")['body']), 'email' => 'awa@example.com', 'password' => 'oldpass123']);
ck('login with OLD password', str_contains($r['loc'], '/login') ? 'rejected' : 'ACCEPTED', 'rejected');
$jar = $save;

// --- toggle ---
$r = req("$base/admin/users/$id/edit");
$r = req("$base/admin/users/$id/toggle", ['_token' => csrf($r['body'])]);
ck('toggle is_active', $r['code'], 302);
ck('  now disabled', App\Models\User::find($id)->is_active ? 'no' : 'yes', 'yes');
$r = req("$base/admin/users");
ck('  badge shows Disabled', str_contains($r['body'], 'Disabled') ? 'yes' : 'no', 'yes');
$r = req("$base/admin/users/$id/edit");
req("$base/admin/users/$id/toggle", ['_token' => csrf($r['body'])]);
ck('  re-enabled', App\Models\User::find($id)->is_active ? 'yes' : 'no', 'yes');

// --- self-protection guards ---
$r = req("$base/admin/users/$me/edit");
$r = req("$base/admin/users/$me/toggle", ['_token' => csrf($r['body'])]);
$r = req("$base/admin/users");
ck('guard: cannot deactivate self', str_contains($r['body'], 'own account') ? 'yes' : 'NO', 'yes');
ck('  admin still active', App\Models\User::find($me)->is_active ? 'yes' : 'no', 'yes');

$r = req("$base/admin/users/$me/edit");
$r = req("$base/admin/users/$me", ['_method' => 'DELETE', '_token' => csrf($r['body'])]);
$r = req("$base/admin/users");
ck('guard: cannot delete self', str_contains($r['body'], 'own account') ? 'yes' : 'NO', 'yes');
ck('  admin still exists', App\Models\User::find($me) ? 'yes' : 'NO', 'yes');

$r = req("$base/admin/users/$me/edit");
$r = req("$base/admin/users/$me", [
    '_method' => 'PUT', '_token' => csrf($r['body']),
    'name' => 'admin', 'email' => 'admin@example.com',
    'password' => '', 'password_confirmation' => '', 'roles' => [2],
]);
$r = req("$base/admin/users");
ck('guard: cannot drop own admin', str_contains($r['body'], 'own admin role') ? 'yes' : 'NO', 'yes');
ck('  admin role intact', App\Models\User::find($me)->roles->pluck('name')->implode(','), 'Admin');

// --- delete ---
$r = req("$base/admin/users/$id/edit");
$r = req("$base/admin/users/$id", ['_method' => 'DELETE', '_token' => csrf($r['body'])]);
ck('delete user', $r['code'], 302);
ck('  row gone', uid('awa@example.com') ? 'no' : 'yes', 'yes');
ck('  pivot rows cleaned', DB::table('role_user')->where('user_id', $id)->exists() ? 'no' : 'yes', 'yes');
ck('user count restored', App\Models\User::count(), $before);

echo "\nALL DONE\n";
