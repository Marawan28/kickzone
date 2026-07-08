<?php

namespace App\Http\Controllers\API\V1\Agent;

use App\Http\Controllers\Controller;
use App\Services\AgentService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(private AgentService $agent) {}

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array',
        ]);

        $reply = $this->agent->chat(
            $request->input('history', []),
            $request->input('message')
        );

        return response()->json(['reply' => $reply]);
    }
}