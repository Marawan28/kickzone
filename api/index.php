<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. تحميل الـ Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. تشغيل التطبيق (تأكد من المسار)
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. معالجة الطلب
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);