<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AiInteractionLog;
use App\Models\Submission;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private AIService $ai) {}

    /**
     * Kirim pesan chat siswa ke Virtual Assistant, simpan ke log.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'message'     => ['required', 'string', 'max:1000'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $userId   = Auth::id();

        // Ambil submission terakhir siswa untuk activity ini
        // Digunakan VA untuk menyesuaikan konteks bimbingan
        $latestSubmission = Submission::where('user_id', $userId)
            ->where('activity_id', $activity->id)
            ->orderBy('attempt', 'desc')
            ->first();

        // Rekonstruksi riwayat chat dari DB (type='chat', bukan type='submit')
        // Riwayat ini dikirim ke AI sebagai konteks percakapan sebelumnya
        $history = AiInteractionLog::where('user_id', $userId)
            ->where('activity_id', $activity->id)
            ->where('type', 'chat')
            ->orderBy('created_at')
            ->get()
            ->flatMap(fn($log) => [
                ['role' => 'user',      'message' => $log->prompt_sent],
                ['role' => 'assistant', 'message' => $log->response_received],
            ])
            ->values()
            ->toArray();

        // Batasi riwayat agar tidak terlalu panjang (hemat token)
        // Ambil N pesan terakhir saja (lihat config/ai.php: chat_history_limit)
        $historyLimit = config('ai.chat_history_limit', 10);
        $history      = array_slice($history, -$historyLimit);

        $result = $this->ai->chat($activity, $request->message, $history, $latestSubmission);

        // Simpan pasangan pesan siswa + respons AI ke log (type='chat')
        AiInteractionLog::create([
            'user_id'           => $userId,
            'activity_id'       => $activity->id,
            'type'              => 'chat',
            'prompt_sent'       => $request->message,
            'response_received' => $result['text'],
            'tokens_used'       => $result['tokensUsed']   ?: null,
            'response_time'     => $result['responseTime'] ?: null,
        ]);

        return response()->json([
            'success'  => true,
            'response' => $result['text'],
        ]);
    }
}
