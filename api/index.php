<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// التحميل من الفولدر الرئيسي (خارج فولدر api)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// صمام أمان لنسخ لارايفل الجديدة
if ($app === true) {
    $app = \Illuminate\Foundation\Application::getInstance();
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();
$kernel->terminate($request, $response);