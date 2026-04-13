<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $baseUrl = rtrim(env('AI_AGENT_URL'), '/');
        $url = $baseUrl . '/api/v1/chat/completions';
        $apiKey = env('AI_AGENT_KEY');

        $messages = $request->history ?? [];
        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = Http::withoutVerifying() 
                ->withToken($apiKey)
                ->post($url, [
                    'messages' => $messages,
                    'stream' => false,
                    'include_functions_info' => true,
                    'include_retrieval_info' => true,
                    'include_guardrails_info' => true
                ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Ditolak oleh Server AI',
                    'status' => $response->status(),
                    'body' => $response->json() 
                ], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Koneksi Gagal (Exception)',
                'message' => $e->getMessage() 
            ], 500);
        }
    }
}