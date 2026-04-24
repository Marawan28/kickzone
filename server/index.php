<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. تحميل المكتبات
require __DIR__ . '/../vendor/autoload.php';

// 2. تشغيل التطبيق
$app = require __DIR__ . '/../bootstrap/app.php';

// صمام أمان لو الـ app رجع true
if ($app === true) {
    $app = \Illuminate\Foundation\Application::getInstance();
}

// 3. التعامل مع الـ Kernel والـ Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// تعديل طريقة الـ capture لضمان وصول الكلاس
$request = \Illuminate\Http\Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);