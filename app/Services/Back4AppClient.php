<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Back4AppClient
{
    public function get($uri)
    {
        return Http::withHeaders([
            'X-Parse-Application-Id' => config('services.b4a.app_id'),
            'X-Parse-REST-API-Key'   => config('services.b4a.rest_key'),
            'Content-Type'           => 'application/json',
        ])->get(config('services.b4a.base_url') . $uri);
    }
}