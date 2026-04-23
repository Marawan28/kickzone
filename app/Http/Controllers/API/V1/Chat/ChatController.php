<?php

namespace App\Http\Controllers\API\V1\Chat;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    // عرض كل الشاتات الخاصة باليوزر
    public function index(): JsonResponse
    {
        $chats = $this->chatService->getUserChats(auth()->id());
        return response()->json($chats);
    }

    // عرض رسائل شات محدد
    public function messages(int $id): JsonResponse
    {
        $messages = $this->chatService->getChatMessages($id);
        return response()->json($messages);
    }

 public function send(Request $request, int $id): JsonResponse
{
    $request->validate([
        'message' => 'required|string|max:1000'
    ]);

    $message = $this->chatService->sendMessage(
        $id, 
        auth()->id(), 
        $request->message
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'Message sent successfully',
        'data'    => $message->load('user:id,name')
    ], 201);
    }

}