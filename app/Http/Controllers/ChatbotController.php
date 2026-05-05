<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Gedung;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        if (!$request->has('message')) {
            return response()->json(['error' => 'Pesan kosong'], 400);
        }

        $message = $request->input('message');
        
        try {
            // Eager load relasi yang ada di database agar AI tahu informasi utuh
            // Pastikan model Gedung sudah memiliki fungsi relasi: fasilitas(), jadwalRuangans(), dll.
            $gedungs = Gedung::with(['fasilitas.jadwalRuangans'])->get()->toJson();
            
            // Opsional: Jika ingin AI tahu setting aplikasi
            // $appSettings = AppSetting::all()->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
        }

        $apiKey = env('GEMINI_API_KEY');
        // Menggunakan versi 1.5-flash yang sangat stabil untuk task JSON/Teks hybrid
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // Prompt engineer untuk membatasi AI
        $systemPrompt = "Anda adalah asisten cerdas WebGIS Politani.
        
        DATA DATABASE KAMPUS:
        $gedungs
        
        ATURAN WAJIB & MUTLAK:
        1. Anda HANYA diizinkan menjawab berdasarkan DATA DATABASE KAMPUS di atas.
        2. Jika user bertanya di luar konteks data kampus, gedung, fasilitas, atau jadwal yang ada di data tersebut, Anda WAJIB menolak dengan sopan (Contoh: 'Maaf, saya hanya asisten WebGIS Politani dan hanya bisa menjawab informasi seputar kampus.').
        3. Jika user menanyakan LOKASI (dimana, letak, arahkan, rute), Anda HANYA boleh menjawab dengan format JSON MURNI tanpa teks pembuka/penutup apapun.
           Format JSON: {\"message\": \"Penjelasan singkat lokasi\", \"lat\": -0.5, \"lng\": 117.1}
        4. Ambil nilai lat dari kolom 'x' dan lng dari kolom 'y'.
        5. Jika user TIDAK menanyakan lokasi, jawab dengan teks biasa yang informatif berdasarkan data.
        6. DILARANG KERAS menggunakan markdown ```json atau ``` dalam respons JSON Anda.";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(60)
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt],
                            ['text' => "Pertanyaan User: " . $message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1, // Diberi sedikit toleransi 0.1 agar bahasa lebih natural tapi tetap patuh aturan
                ]
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'API Error'], 500);
            }

            $result = $response->json();
            $aiResponseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            $cleanContent = str_replace(['```json', '```', '\n'], '', trim($aiResponseText));

            return response()->json([
                'choices' => [
                    [
                        'message' => [
                            'content' => $cleanContent
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}