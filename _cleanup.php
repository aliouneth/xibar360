<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Remove test accounts created by the verification scripts.
foreach (['editor.test@example.com', 'awa@example.com', 'bad@example.com'] as $email) {
    $u = User::where('email', $email)->first();
    if ($u) {
        $u->roles()->detach();
        $u->delete();
        echo "deleted test user: {$email} (id={$u->id})\n";
    }
}

// Purge any orphaned role_user rows.
$n = DB::table('role_user')->whereNotIn('user_id', User::pluck('id'))->delete();
echo "removed orphaned role_user rows: {$n}\n";

echo "\nremaining users:\n";
foreach (User::with('roles')->get() as $u) {
    printf("  id=%-3d %-26s roles=[%s]\n", $u->id, $u->email, $u->roles->pluck('name')->implode(','));
}

echo "\norphans left: ".DB::table('role_user')->whereNotIn('user_id', User::pluck('id'))->count()."\n";
