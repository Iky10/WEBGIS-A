<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Disable CSRF for testing
$app->instance(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

// Authenticate as admin user
$user = \App\Models\User::where('email', 'admin@webgis.com')->first();
if ($user) {
    \Auth::login($user);
}

$request = Illuminate\Http\Request::create('/pengajuan_gedungs/3/status', 'PATCH', [
    'status' => 'disetujui',
    'catatan_admin' => 'ya ya saya setuju test via cli'
]);

$response = $kernel->handle($request);
echo 'STATUS: ' . $response->getStatusCode() . PHP_EOL;
if ($response->getStatusCode() == 302) {
    echo 'REDIRECT TO: ' . $response->headers->get('Location') . PHP_EOL;
    $errors = session('errors');
    if ($errors) {
        echo "ERRORS: " . json_encode($errors->getMessages()) . PHP_EOL;
    }
}
$p = \App\Models\PengajuanGedung::find(3);
echo "New status in DB: " . $p->status . " | Catatan: " . $p->catatan_admin . PHP_EOL;
