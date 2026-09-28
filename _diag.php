<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== roles (id => name) ===\n";
foreach (App\Models\Role::orderBy('id')->get() as $r) {
    echo "  {$r->id} => {$r->name}\n";
}

echo "\n=== users ===\n";
foreach (User::with('roles')->get() as $u) {
    printf("  id=%-3d %-28s active=%d roles=[%s]\n",
        $u->id, $u->email, $u->is_active, $u->roles->pluck('name')->implode(','));
}

echo "\n=== ORPHANED role_user rows (user no longer exists) ===\n";
$orphans = DB::table('role_user')
    ->whereNotIn('user_id', User::pluck('id'))
    ->get();
if ($orphans->isEmpty()) {
    echo "  none\n";
} else {
    foreach ($orphans as $o) {
        echo "  user_id={$o->user_id} role_id={$o->role_id}\n";
    }
    echo '  TOTAL: '.$orphans->count()."\n";
}
