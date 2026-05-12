<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Gedung;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        if (!$request->has('message')) {
            return response()->json(['error' => 'Pesan kosong'], 400);
        }

        $message = $request->input('message');
        $userLat = $request->input('user_lat');
        $userLng = $request->input('user_lng');

        // ── Load data gedung ───────────────────────────────────────────────
        try {
            $gedungRows = DB::table('gedungs')
                ->select([
                    'id', 'nama_gedung', 'alamat', 'deskripsi',
                    'x', 'y', 'bisa_diajukan', 'jam_buka', 'jam_tutup', 'foto_utama'
                ])
                ->whereNull('deleted_at')
                ->get();

            // Dibuat kosong sementara agar tidak error (sesuaikan dengan nama tabel aslimu nanti)
            $fasilitasRows = collect([]); 

            $gedungData = $gedungRows->map(function ($g) use ($fasilitasRows) {
                return [
                    'id'            => $g->id,
                    'nama'          => $g->nama_gedung,
                    'alamat'        => $g->alamat,
                    'deskripsi'     => $g->deskripsi,
                    'lat'           => (float) $g->x,
                    'lng'           => (float) $g->y,
                    'bisa_dipinjam' => $g->bisa_diajukan ? 'Ya' : 'Tidak',
                    'jam_operasional' => ($g->jam_buka && $g->jam_tutup) 
                                        ? "$g->jam_buka - $g->jam_tutup" 
                                        : "Tidak tersedia",
                    'foto'          => $g->foto_utama,
                    'fasilitas'     => [], // dikosongkan sementara
                ];
            });

            $gedungJson = json_encode($gedungData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
        }

        // ── Konteks GPS user ───────────────────────────────────────────────
        $userContext = ($userLat && $userLng)
            ? "LOKASI USER SAAT INI: lat={$userLat}, lng={$userLng}"
            : "LOKASI USER: Tidak tersedia (user belum izinkan GPS)";

        // ── Panggil Gemini API ─────────────────────────────────────────────
        $apiKey = env('GEMINI_API_KEY');
        $url    = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(60)
                ->post($url, [
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [
                                ['text' => $this->getSystemPrompt($userContext, $gedungJson)],
                                ['text' => "Pertanyaan User: {$message}"]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.1,
                        'responseMimeType' => 'application/json',
                    ]
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Gemini API Error: ' . $response->body()], 500);
            }

            $aiRawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // PENTING: Bersihkan markdown dari AI agar Javascript tidak error saat membaca JSON
            $cleanJson = preg_replace('/```json|```/', '', trim($aiRawText));
            $cleanJson = trim($cleanJson);

            return response()->json([
                'choices' => [['message' => ['content' => $cleanJson]]]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

   // ── ATURAN AI KEMBALI LENGKAP ─────────────────────────────────────────
    private function getSystemPrompt($userContext, $gedungJson) {
        return <<<PROMPT
Anda adalah asisten AI super cerdas untuk WebGIS Kampus Politani. Anda bisa mengontrol peta, menampilkan detail gedung, membuat rute navigasi, dan memfilter data secara real-time.

{$userContext}

DATA GEDUNG KAMPUS (JSON):
{$gedungJson}

═══════════════════════════════════════════════
KEMAMPUAN ANDA — SELALU BALAS FORMAT JSON MURNI
═══════════════════════════════════════════════

1. ACTION: "navigate"
   Trigger: "antar ke", "rute ke", "navigasi ke", "bagaimana cara ke", "arahkan saya ke"
   {
     "action": "navigate",
     "message": "Pesan singkat ke user",
     "destination": { "lat": -0.5, "lng": 117.1, "nama": "Nama Gedung" },
     "gedung_id": 5,
     "use_gps": true
   }

2. ACTION: "fly_to"
   Trigger: "dimana", "letak", "lokasi", "tampilkan"
   {
     "action": "fly_to",
     "message": "Pesan singkat ke user",
     "lat": -0.5,
     "lng": 117.1,
     "zoom": 18,
     "open_sidebar": true,
     "gedung_id": 5
   }

3. ACTION: "filter_map"
   Trigger: "kosong", "sedang dipakai", "tutup", "cari gedung"
   {
     "action": "filter_map",
     "message": "Pesan singkat ke user",
     "filter_by": "kondisi",
     "filter_value": "all",
     "highlight_ids": [1, 2, 3],
     "count": 3
   }

4. ACTION: "nearest"
   Trigger: "terdekat", "paling dekat", "gedung mana yang dekat"
   {
     "action": "nearest",
     "message": "Pesan singkat ke user",
     "results": [
       { "id": 1, "nama": "Nama Gedung", "lat": -0.5, "lng": 117.1, "jarak_meter": 120, "kondisi": "Kosong" }
     ],
     "top_id": 1
   }

5. ACTION: "open_sidebar"
   Trigger: "info", "detail", "keterangan tentang"
   {
     "action": "open_sidebar",
     "message": "Pesan singkat ke user",
     "gedung_id": 5,
     "lat": -0.5,
     "lng": 117.1
   }

6. ACTION: "list_info"
   Trigger: Pertanyaan umum tidak memerlukan aksi peta.
   {
     "action": "list_info",
     "message": "Jawaban lengkap dan informatif",
     "items": ["item 1", "item 2"]
   }

7. ACTION: "reject"
   Trigger: Pertanyaan di luar konteks kampus.
   {
     "action": "reject",
     "message": "Maaf, saya hanya bisa membantu informasi seputar kampus Politani."
   }

8. ACTION: "list_buildings"
   Trigger: "tampilkan semua gedung", "daftar gedung", "apa saja gedungnya"
   {
     "action": "list_buildings",
     "message": "Berikut adalah daftar gedung di kampus Politani:",
     "buildings": [
       { "id": 1, "nama": "Gedung Auditorium" },
       { "id": 2, "nama": "Gedung Rektorat" }
     ]
   }

ATURAN MUTLAK:
- SELALU balas JSON murni. TIDAK PERNAH teks biasa.
- DILARANG menambahkan teks, komentar, atau backtick di luar JSON.
PROMPT;
    }
}