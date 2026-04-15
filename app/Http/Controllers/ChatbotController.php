<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('chatbot.index');
    }

    public function chat(Request $request)
    {
        $message = trim($request->input('message'));
        
        // Jika pesan kosong
        if (empty($message)) {
            return response()->json([
                'reply' => 'Mohon masukkan pesan terlebih dahulu.',
                'success' => false
            ]);
        }

        try {
            $reply = $this->getAIResponse($message);
            
            return response()->json([
                'reply' => $reply,
                'success' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Chatbot Error: ' . $e->getMessage());
            
            return response()->json([
                'reply' => 'Maaf, terdapat kesalahan. Silakan coba lagi.',
                'success' => false
            ]);
        }
    }

    private function getAIResponse($userMessage)
    {
        $user = Auth::user();
        $userName = $user->name ?? 'Pengguna';
        
        // Konteks khusus untuk chatbot - lebih fleksibel
        $systemPrompt = "Anda adalah chatbot Absensi PKL yang ramah dan membantu siswa bernama $userName.\n\n" .
                       "Prioritas Anda:\n" .
                       "1. Bantu mengenai status absensi dan program PKL\n" .
                       "2. Jawab pertanyaan umum (termasuk matematika, sejarah, sains, dll)\n" .
                       "3. Berikan informasi tentang sistem\n\n" .
                       "Jawab dengan singkat, profesional, ramah, dan selalu dalam Bahasa Indonesia.\n" .
                       "Jika ditanya tentang topik sensitive atau diluar hukum, tolak dengan halus.";

        $apiKey = env('GROQ_API_KEY');
        
        if (!$apiKey) {
            throw new \Exception('GROQ_API_KEY tidak dikonfigurasi');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile', // Model Llama 3.3 70B yang sangat powerful
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 200,
        ]);

        if (!$response->successful()) {
            \Log::error('Groq API Error: ' . $response->body());
            throw new \Exception('Gagal mendapatkan respons dari AI');
        }

        $data = $response->json();
        
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Format respons AI tidak valid');
        }

        return trim($data['choices'][0]['message']['content']);
    }
}

