<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hash = Illuminate\Support\Facades\Hash::make('123456');
$n = App\Models\User::query()->update(['mat_khau' => $hash, 'trang_thai' => 1]);
echo "updated {$n}\n";
foreach (App\Models\User::all(['id', 'email', 'vai_tro', 'trang_thai']) as $u) {
    echo "{$u->id} {$u->email} {$u->vai_tro}\n";
}
echo 'cas: ' . App\Models\CaLamViec::count() . "\n";
echo 'ok hash: ' . (Illuminate\Support\Facades\Hash::check('123456', $hash) ? 'yes' : 'no') . "\n";
