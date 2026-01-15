<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OpenAIService;

class ChatController extends Controller
{
    public function index()
    {
        return view('user.chat');
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'messages' => 'nullable|array', // Conversation history
        ]);

        try {
            // Prepare messages array for OpenAI
            $messages = $validated['messages'] ?? [];

            // Ensure messages array is properly formatted
            if (!is_array($messages)) {
                $messages = [];
            }

            // Validate each message in history
            $validMessages = [];
            foreach ($messages as $msg) {
                if (isset($msg['role']) && isset($msg['content']) &&
                    in_array($msg['role'], ['user', 'model', 'assistant'])) {
                    $validMessages[] = [
                        'role' => $msg['role'],
                        'content' => (string) $msg['content']
                    ];
                }
            }

            // Add the new user message
            $validMessages[] = [
                'role' => 'user',
                'content' => $validated['message']
            ];

            // Get AI response
            $response = OpenAIService::chat($validMessages);

            return response()->json([
                'status' => 'success',
                'message' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Chat Controller Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
