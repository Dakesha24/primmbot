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

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'message'     => ['required', 'string', 'max:1000'],
        ]);

        $activity = Activity::findOrFail($request->activity_id);
        $userId   = Auth::id();

        // Ambil submission terakhir siswa untuk aktivitas ini
        $latestSubmission = Submission::where('user_id', $userId)
            ->where('activity_id', $activity->id)
            ->orderBy('attempt', 'desc')
            ->first();

        // Rekonstruksi riwayat chat dari DB (type='chat')
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

        // Batasi history ke 10 pesan terakhir (5 pasang) agar hemat token
        $history = array_slice($history, -10);

        $response = $this->ai->chat($activity, $request->message, $history, $latestSubmission);

        // Simpan interaksi ke log (type='chat')
        AiInteractionLog::create([
            'user_id'           => $userId,
            'activity_id'       => $activity->id,
            'type'              => 'chat',
            'prompt_sent'       => $request->message,
            'response_received' => $response,
        ]);

        return response()->json([
            'success'  => true,
            'response' => $response,
        ]);
    }
}
