<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = App\Models\User::where('email', 'admin@happytime.vn')->first();
auth()->login($admin);

try {
    $html = app('view')->make('nhanvien.index', [
        'danhsachNV' => App\Models\NhanVien::with(['phongban', 'chucvu'])->get(),
        'stats' => ['total' => 1, 'active' => 1, 'inactive' => 0, 'pending' => 0, 'admins' => 1],
        'status' => 'all',
        'q' => '',
        'role' => '',
    ])->render();
    echo "RENDER_OK " . strlen($html) . " has_users=" . (strpos($html, 'Quản lý') !== false || strpos($html, 'Users') !== false ? 'yes' : 'no') . "\n";
} catch (Throwable $e) {
    echo "RENDER_FAIL " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
}
