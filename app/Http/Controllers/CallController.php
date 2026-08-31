<?php

namespace App\Http\Controllers;

use App\Events\CallSignal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CallController extends Controller
{
    /**
     * Initiate a call – stores pending call state only.
     * The actual call-request signal is sent from the frontend.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'to'       => 'required|integer|exists:users,id',
            'callType' => 'required|in:audio,video',
        ]);

        $from     = Auth::id();
        $to       = (int) $request->to;
        $callType = $request->callType;

        // Store pending call so recipient can see it on poll / push (optional)
        $callKey = 'active-call-' . min($from, $to) . '-' . max($from, $to);
        Cache::put($callKey, [
            'from'      => $from,
            'to'        => $to,
            'callType'  => $callType,
            'startedAt' => now()->toISOString(),
        ], now()->addMinutes(2));

        // Do NOT broadcast call-request here – frontend will send it with caller details
        // This prevents duplicate signals that cause call-busy race conditions.

        return response()->json(['status' => 'ringing']);
    }

    /**
     * Send a WebRTC signalling message (offer / answer / ice-candidate).
     */
    public function signal(Request $request)
    {
        $request->validate([
            'to'      => 'required|integer|exists:users,id',
            'type'    => 'required|string',
            'payload' => 'present|array',
        ]);

        $from = Auth::id();
        $to   = (int) $request->to;

        broadcast(new CallSignal($from, $to, $request->type, $request->payload))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    /**
     * End / decline / busy – cleans up cache and broadcasts termination signal.
     */
    public function end(Request $request)
    {
        $request->validate([
            'to'   => 'required|integer|exists:users,id',
            'type' => 'required|in:call-ended,call-declined,call-busy',
        ]);

        $from = Auth::id();
        $to   = (int) $request->to;

        $callKey = 'active-call-' . min($from, $to) . '-' . max($from, $to);
        Cache::forget($callKey);

        broadcast(new CallSignal($from, $to, $request->type, []))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
