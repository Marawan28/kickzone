<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class MigrateToBack4app extends Command
{
    protected $signature = 'app:migrate-json';
    protected $description = 'نقل البيانات من ملف JSON إلى Back4App';

    public function handle()
    {
        // 1. مسار الملف (حط الملف في فولدر database)
        $path = database_path('smart_match_hub.json'); 
        
        if (!File::exists($path)) {
            $this->error("الملف غير موجود في: " . $path);
            return;
        }

        $data = json_decode(File::get($path), true);

        // 2. اللوب على الجداول
        foreach ($data as $item) {
            if ($item['type'] === 'table') {
                $tableName = $item['name'];
                $this->info("جاري معالجة جدول: " . $tableName);

                foreach ($item['data'] as $row) {
                    // إرسال البيانات للـ API
                    Http::withHeaders([
                        'X-Parse-Application-Id' => env('BACK4APP_APP_ID'),
                        'X-Parse-REST-API-Key'   => env('BACK4APP_REST_KEY'),
                        'Content-Type'           => 'application/json',
                    ])->post('https://parseapi.back4app.com/classes/' . ucfirst($tableName), $row);
                }
            }
        }

        $this->info('تم رفع البيانات بنجاح!');
    }
}