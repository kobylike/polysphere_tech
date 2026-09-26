<div>
    {{-- ══════════════════════════════════════════════════════════
    OUTGOING CALL OVERLAY
    ══════════════════════════════════════════════════════════ --}}
    <div id="call-outgoing-overlay" class="call-overlay-fullscreen">
        <div class="call-overlay-card">
            <button type="button" class="call-corner-btn" onclick="window.CallManager.hangUp()" title="Cancel (Esc)">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="call-avatar-wrap">
                <div class="call-ring-anim"></div>
                <div class="call-ring-anim" style="animation-delay:.5s"></div>
                <div class="call-ring-anim" style="animation-delay:1s"></div>
                <img id="call-out-avatar" src="" alt="" class="call-avatar-lg">
            </div>

            <div id="call-out-name" class="call-overlay-name"></div>
            <div id="call-out-type" class="call-overlay-sub"></div>

            <div class="call-overlay-timer" id="call-out-timer">Ringing…</div>

            <div class="call-overlay-actions">
                <button type="button" onclick="window.CallManager.hangUp()" class="call-action-btn call-end-btn"
                    title="Cancel">
                    <i class="bi bi-telephone-x-fill"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
    INCOMING CALL OVERLAY
    ══════════════════════════════════════════════════════════ --}}
    <div id="call-incoming-overlay" class="call-overlay-fullscreen">
        <div class="call-overlay-card">
            <button type="button" class="call-corner-btn" onclick="window.CallManager.declineCall()" title="Decline">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="call-avatar-wrap">
                <div class="call-ring-anim"></div>
                <div class="call-ring-anim" style="animation-delay:.5s"></div>
                <div class="call-ring-anim" style="animation-delay:1s"></div>
                <img id="call-in-avatar" src="" alt="" class="call-avatar-lg">
            </div>

            <div id="call-in-name" class="call-overlay-name"></div>
            <div id="call-in-type" class="call-overlay-sub"></div>

            <div class="call-overlay-actions">
                <div class="call-overlay-action-group">
                    <button type="button" onclick="window.CallManager.acceptCall()"
                        class="call-action-btn call-accept-btn" title="Accept">
                        <i class="bi bi-telephone-fill"></i>
                    </button>
                    <span>Accept</span>
                </div>
                <div class="call-overlay-action-group">
                    <button type="button" onclick="window.CallManager.acceptCall(true)"
                        class="call-action-btn call-video-btn" title="Accept with video">
                        <i class="bi bi-camera-video-fill"></i>
                    </button>
                    <span>Video</span>
                </div>
                <div class="call-overlay-action-group">
                    <button type="button" onclick="window.CallManager.declineCall()"
                        class="call-action-btn call-end-btn" title="Decline">
                        <i class="bi bi-telephone-x-fill"></i>
                    </button>
                    <span>Decline</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
    ACTIVE CALL WINDOW
    ══════════════════════════════════════════════════════════ --}}
    <div id="call-active-window" class="call-window" data-snap="br">

        {{-- Chrome --}}
        <div id="call-chrome" class="call-chrome">
            <div class="call-chrome-left">
                <span class="call-status-dot" id="call-status-dot"></span>
                <span class="call-chrome-title" id="call-chrome-title">Connected</span>
                <span class="call-chrome-timer" id="call-chrome-timer">00:00</span>
            </div>
            <div class="call-chrome-right">
                <button type="button" class="call-chrome-btn" id="btn-pip" onclick="window.CallManager.togglePiP()"
                    title="Picture-in-Picture (P)">
                    <i class="bi bi-pip"></i>
                </button>
                <button type="button" class="call-chrome-btn" id="btn-fullscreen"
                    onclick="window.CallManager.toggleFullscreen()" title="Fullscreen (F)">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <button type="button" class="call-chrome-btn" id="btn-minimize" onclick="window.CallManager.minimize()"
                    title="Minimize (N)">
                    <i class="bi bi-dash-lg"></i>
                </button>
                <button type="button" class="call-chrome-btn call-chrome-close" onclick="window.CallManager.hangUp()"
                    title="End (E)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Video/avatar area --}}
        <div id="call-video-area" class="call-video-area">
            <video id="remote-video" autoplay playsinline class="remote-video"></video>

            <div id="remote-audio-avatar" class="remote-audio-avatar">
                <div class="audio-pulse-wrap">
                    <div id="call-avatar-ring" class="audio-pulse-ring"></div>
                    <div class="audio-pulse-ring" style="animation-delay:.6s"></div>
                    <img id="call-active-avatar" src="" alt="" class="call-active-avatar">
                </div>
                <div id="call-active-name" class="call-active-name"></div>
                <div class="call-active-sub">Audio call</div>
            </div>

            <video id="local-video" autoplay muted playsinline class="local-video"></video>

            <div id="call-quality-badge" class="call-quality-badge">
                <div class="quality-bars">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span id="call-quality-label">HD</span>
            </div>

            <div id="call-rec-indicator" class="call-rec-indicator">
                <span class="rec-dot"></span> REC
            </div>

            <div id="call-share-indicator" class="call-share-indicator">
                <i class="bi bi-display"></i> Sharing screen
            </div>

            <div id="call-reactions-layer" class="call-reactions-layer"></div>

            <div id="call-stats-panel" class="call-stats-panel">
                <div class="stats-row"><span>Bitrate</span><b id="stat-bitrate">—</b></div>
                <div class="stats-row"><span>RTT</span><b id="stat-rtt">—</b></div>
                <div class="stats-row"><span>Packet loss</span><b id="stat-loss">—</b></div>
                <div class="stats-row"><span>Jitter</span><b id="stat-jitter">—</b></div>
                <div class="stats-row"><span>Resolution</span><b id="stat-res">—</b></div>
                <div class="stats-row"><span>Codec</span><b id="stat-codec">—</b></div>
            </div>

            <div id="call-reaction-bar" class="call-reaction-bar">
                <button type="button" onclick="window.CallManager.sendReaction('👍')">👍</button>
                <button type="button" onclick="window.CallManager.sendReaction('❤️')">❤️</button>
                <button type="button" onclick="window.CallManager.sendReaction('😂')">😂</button>
                <button type="button" onclick="window.CallManager.sendReaction('😮')">😮</button>
                <button type="button" onclick="window.CallManager.sendReaction('👏')">👏</button>
                <button type="button" onclick="window.CallManager.sendReaction('🎉')">🎉</button>
            </div>

            <div id="volume-slider-wrap" class="volume-slider-wrap">
                <i class="bi bi-volume-down-fill"></i>
                <input id="volume-slider" type="range" min="0" max="100" value="100"
                    oninput="window.CallManager.setVolume(this.value)">
                <i class="bi bi-volume-up-fill"></i>
            </div>
        </div>

        {{-- Controls --}}
        <div class="call-controls" id="call-controls">
            <div class="call-ctrl-group">
                <button type="button" id="btn-toggle-mic" onclick="window.CallManager.toggleMic()"
                    class="call-ctrl-btn active" title="Mute (M)">
                    <i class="bi bi-mic-fill"></i>
                </button>
                <span class="call-ctrl-label">Mute</span>
            </div>

            <div class="call-ctrl-group" id="cam-ctrl-wrap">
                <button type="button" id="btn-toggle-cam" onclick="window.CallManager.toggleCamera()"
                    class="call-ctrl-btn active" title="Camera (V)">
                    <i class="bi bi-camera-video-fill"></i>
                </button>
                <span class="call-ctrl-label">Camera</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-toggle-speaker" onclick="window.CallManager.toggleSpeaker()"
                    class="call-ctrl-btn active" title="Speaker (S)">
                    <i class="bi bi-volume-up-fill"></i>
                </button>
                <span class="call-ctrl-label">Speaker</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-screenshare" onclick="window.CallManager.toggleScreenShare()"
                    class="call-ctrl-btn" title="Share screen">
                    <i class="bi bi-display"></i>
                </button>
                <span class="call-ctrl-label">Share</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-reactions" onclick="window.CallManager.toggleReactionBar()"
                    class="call-ctrl-btn" title="Reactions">
                    <i class="bi bi-emoji-smile"></i>
                </button>
                <span class="call-ctrl-label">React</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-devices" onclick="window.CallManager.toggleDevicePanel()"
                    class="call-ctrl-btn" title="Devices">
                    <i class="bi bi-sliders"></i>
                </button>
                <span class="call-ctrl-label">Devices</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-stats" onclick="window.CallManager.toggleStats()" class="call-ctrl-btn"
                    title="Stats">
                    <i class="bi bi-graph-up"></i>
                </button>
                <span class="call-ctrl-label">Stats</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" id="btn-record" onclick="window.CallManager.toggleRecording()"
                    class="call-ctrl-btn" title="Record">
                    <i class="bi bi-record-circle"></i>
                </button>
                <span class="call-ctrl-label">Record</span>
            </div>

            <div class="call-ctrl-group">
                <button type="button" onclick="window.CallManager.hangUp()" class="call-action-btn call-end-btn"
                    title="End (E)">
                    <i class="bi bi-telephone-x-fill"></i>
                </button>
                <span class="call-ctrl-label">End</span>
            </div>
        </div>

        {{-- Device picker --}}
        <div id="device-panel" class="device-panel">
            <div class="device-section">
                <label><i class="bi bi-mic"></i> Microphone</label>
                <select id="select-mic" onchange="window.CallManager.switchDevice('audioinput', this.value)"></select>
            </div>
            <div class="device-section">
                <label><i class="bi bi-camera-video"></i> Camera</label>
                <select id="select-cam" onchange="window.CallManager.switchDevice('videoinput', this.value)"></select>
            </div>
            <div class="device-section">
                <label><i class="bi bi-volume-up"></i> Speaker</label>
                <select id="select-speaker"
                    onchange="window.CallManager.switchDevice('audiooutput', this.value)"></select>
            </div>
            <div class="device-section">
                <label><i class="bi bi-bell"></i> Ringtone</label>
                <select id="select-ringtone" onchange="window.CallManager.setRingtoneMode(this.value)">
                    <option value="default">Default</option>
                    <option value="silent">Silent</option>
                    <option value="vibrate">Vibrate only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- MINIMIZED PILL --}}
    <div id="call-minimized-pill" class="call-minimized-pill" onclick="window.CallManager.restore()">
        <div class="pill-avatar-wrap">
            <img id="pill-avatar" src="" alt="">
            <span class="pill-pulse"></span>
        </div>
        <div class="pill-info">
            <div id="pill-name">—</div>
            <div id="pill-timer" class="pill-timer">00:00</div>
        </div>
        <button type="button" class="pill-btn pill-btn-end"
            onclick="event.stopPropagation(); window.CallManager.hangUp()" title="End">
            <i class="bi bi-telephone-x-fill"></i>
        </button>
        <button type="button" class="pill-btn" onclick="event.stopPropagation(); window.CallManager.toggleMic()"
            id="pill-mic-btn" title="Mute">
            <i class="bi bi-mic-fill"></i>
        </button>
    </div>

    {{-- MISSED CALL TOAST --}}
    <div id="missed-call-toast" class="missed-call-toast">
        <img id="missed-avatar" src="" alt="">
        <div class="missed-info">
            <b id="missed-name">—</b>
            <span>Missed call</span>
        </div>
        <button type="button" class="missed-callback" onclick="window.CallManager.callbackMissed()">
            <i class="bi bi-telephone-fill"></i> Call back
        </button>
        <button type="button" class="missed-dismiss"
            onclick="document.getElementById('missed-call-toast').classList.remove('show')">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <style>
        /* Force overlays to escape any transformed ancestor */
        #call-outgoing-overlay,
        #call-incoming-overlay,
        #call-active-window,
        #call-minimized-pill,
        #missed-call-toast {
            position: fixed !important;
        }

        /* Fullscreen overlays */
        .call-overlay-fullscreen {
            display: none;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100dvh !important;
            z-index: 99000 !important;
            background: radial-gradient(circle at center, rgba(20, 20, 50, .95), rgba(5, 5, 20, .98));
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            align-items: center;
            justify-content: center;
            animation: overlay-fade .25s ease;
            overflow: hidden;
            padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
        }

        .call-overlay-fullscreen[style*="display: flex"],
        .call-overlay-fullscreen[style*="display:flex"] {
            display: flex !important;
        }

        @keyframes overlay-fade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .call-overlay-card {
            background: linear-gradient(160deg, #1a1a2e, #16213e);
            border-radius: 26px;
            padding: 48px 60px 44px;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .65), 0 0 0 1px rgba(255, 255, 255, .05);
            min-width: 340px;
            color: #fff;
            position: relative;
            max-width: 90vw;
        }

        .call-corner-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s;
        }

        .call-corner-btn:hover {
            background: rgba(229, 62, 62, .4);
        }

        .call-avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 22px;
        }

        .call-ring-anim {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 2px solid rgba(99, 179, 237, .5);
            animation: ring-pulse 2s ease-out infinite;
        }

        @keyframes ring-pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: .85;
            }

            100% {
                transform: translate(-50%, -50%) scale(2.4);
                opacity: 0;
            }
        }

        .call-avatar-lg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 1;
            border: 3px solid rgba(255, 255, 255, .25);
            box-shadow: 0 0 40px rgba(99, 179, 237, .35);
        }

        .call-overlay-name {
            font-size: 1.45rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .call-overlay-sub {
            font-size: .85rem;
            color: #a0aec0;
            margin-bottom: 14px;
        }

        .call-overlay-timer {
            font-size: .8rem;
            color: #68d391;
            margin-bottom: 28px;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 600;
        }

        .call-overlay-actions {
            display: flex;
            justify-content: center;
            gap: 28px;
            flex-wrap: wrap;
        }

        .call-overlay-action-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .call-overlay-action-group span {
            color: #a0aec0;
            font-size: .75rem;
        }

        .call-action-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            transition: transform .15s, filter .15s, box-shadow .15s;
            color: #fff;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .call-action-btn:hover {
            transform: scale(1.08);
            filter: brightness(1.15);
        }

        .call-action-btn:active {
            transform: scale(.95);
        }

        .call-accept-btn {
            background: linear-gradient(135deg, #38a169, #276749);
            box-shadow: 0 6px 20px rgba(56, 161, 105, .55);
        }

        .call-video-btn {
            background: linear-gradient(135deg, #3182ce, #2c5282);
            box-shadow: 0 6px 20px rgba(49, 130, 206, .55);
        }

        .call-end-btn {
            background: linear-gradient(135deg, #e53e3e, #9b2c2c);
            box-shadow: 0 6px 20px rgba(229, 62, 62, .55);
        }

        /* Active call window — desktop floating, hidden by default */
        .call-window {
            bottom: 24px;
            right: 24px;
            z-index: 89500 !important;
            width: 380px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .65), 0 0 0 1px rgba(255, 255, 255, .06);
            background: #0d1117;
            transition: box-shadow .2s;
            display: none;
            /* ← JS sets display:block when a call starts */
            max-width: 95vw;
        }

        .call-window.dragging {
            transition: none !important;
        }

        .call-window.snapping {
            transition: left .25s ease, top .25s ease, right .25s ease, bottom .25s ease;
        }

        .call-chrome {
            background: rgba(0, 0, 0, .4);
            color: #fff;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: grab;
            user-select: none;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
        }

        .call-chrome:active {
            cursor: grabbing;
        }

        .call-chrome-left,
        .call-chrome-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .call-chrome-title {
            font-size: .75rem;
            opacity: .8;
        }

        .call-chrome-timer {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .05em;
            color: #68d391;
        }

        .call-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #68d391;
            box-shadow: 0 0 8px rgba(104, 211, 145, .7);
        }

        .call-status-dot.warn {
            background: #f6e05e;
            box-shadow: 0 0 8px rgba(246, 224, 94, .7);
        }

        .call-status-dot.bad {
            background: #fc8181;
            box-shadow: 0 0 8px rgba(252, 129, 129, .7);
        }

        .call-chrome-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: .8rem;
            transition: background .15s;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .call-chrome-btn:hover {
            background: rgba(255, 255, 255, .18);
        }

        .call-chrome-close:hover {
            background: rgba(229, 62, 62, .55);
        }

        .call-video-area {
            position: relative;
            background: #161b22;
            height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .remote-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .local-video {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 90px;
            height: 68px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .25);
            display: none;
            z-index: 2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .4);
        }

        .remote-audio-avatar {
            text-align: center;
            z-index: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .audio-pulse-wrap {
            position: relative;
            display: inline-block;
        }

        .audio-pulse-ring {
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px solid rgba(99, 179, 237, .35);
            animation: audio-pulse 2.2s ease-in-out infinite;
        }

        @keyframes audio-pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: .35;
            }

            50% {
                transform: scale(1.18);
                opacity: .85;
            }
        }

        .call-active-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .2);
            position: relative;
            z-index: 1;
        }

        .call-active-name {
            color: #fff;
            font-weight: 600;
            font-size: 1.05rem;
            margin-top: 14px;
        }

        .call-active-sub {
            color: #a0aec0;
            font-size: .75rem;
            margin-top: 2px;
        }

        .call-quality-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
            background: rgba(0, 0, 0, .55);
            border-radius: 12px;
            padding: 5px 9px;
            display: none;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .quality-bars {
            display: flex;
            gap: 2px;
            align-items: flex-end;
            height: 12px;
        }

        .quality-bars span {
            width: 3px;
            background: rgba(255, 255, 255, .25);
            border-radius: 1px;
        }

        .quality-bars span:nth-child(1) {
            height: 4px;
        }

        .quality-bars span:nth-child(2) {
            height: 7px;
        }

        .quality-bars span:nth-child(3) {
            height: 10px;
        }

        .quality-bars span:nth-child(4) {
            height: 12px;
        }

        .quality-bars.q4 span {
            background: #68d391;
        }

        .quality-bars.q3 span:nth-child(-n+3) {
            background: #68d391;
        }

        .quality-bars.q2 span:nth-child(-n+2) {
            background: #f6e05e;
        }

        .quality-bars.q1 span:nth-child(1) {
            background: #fc8181;
        }

        .call-quality-badge #call-quality-label {
            color: #fff;
            font-size: .7rem;
            font-weight: 600;
        }

        .call-rec-indicator,
        .call-share-indicator {
            position: absolute;
            z-index: 2;
            display: none;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .06em;
            padding: 4px 9px;
            border-radius: 10px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            gap: 5px;
        }

        .call-rec-indicator {
            top: 10px;
            right: 10px;
            background: rgba(229, 62, 62, .85);
            color: #fff;
        }

        .call-rec-indicator .rec-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            animation: blink 1.2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .25;
            }
        }

        .call-share-indicator {
            top: 10px;
            right: 10px;
            background: rgba(49, 130, 206, .85);
            color: #fff;
        }

        .call-reactions-layer {
            position: absolute;
            inset: 0;
            z-index: 5;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-emoji {
            position: absolute;
            bottom: 20px;
            font-size: 2rem;
            animation: float-up 2.6s ease-out forwards;
            user-select: none;
        }

        @keyframes float-up {
            0% {
                transform: translateY(0) scale(.8);
                opacity: 0;
            }

            15% {
                transform: translateY(-10px) scale(1.15);
                opacity: 1;
            }

            100% {
                transform: translateY(-220px) scale(1.4);
                opacity: 0;
            }
        }

        .call-reaction-bar {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            z-index: 6;
            opacity: 0;
            pointer-events: none;
            background: rgba(0, 0, 0, .65);
            border-radius: 26px;
            padding: 6px 10px;
            display: flex;
            gap: 4px;
            transition: opacity .2s, transform .2s;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .call-reaction-bar.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        .call-reaction-bar button {
            background: none;
            border: none;
            font-size: 1.35rem;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 10px;
            transition: transform .12s, background .12s;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .call-reaction-bar button:hover {
            background: rgba(255, 255, 255, .15);
            transform: scale(1.2);
        }

        .volume-slider-wrap {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            z-index: 6;
            opacity: 0;
            pointer-events: none;
            background: rgba(0, 0, 0, .65);
            border-radius: 26px;
            padding: 8px 14px;
            display: flex;
            gap: 10px;
            align-items: center;
            color: #fff;
            font-size: .85rem;
            transition: opacity .2s, transform .2s;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .volume-slider-wrap.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        .volume-slider-wrap input {
            width: 140px;
        }

        .call-stats-panel {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, .75);
            color: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: .7rem;
            z-index: 7;
            display: none;
            min-width: 170px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .call-stats-panel.show {
            display: block;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .stats-row span {
            color: #a0aec0;
        }

        .stats-row b {
            color: #fff;
            font-weight: 600;
        }

        .call-controls {
            background: #0d1117;
            padding: 14px 16px 18px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 4px;
            transition: opacity .3s, max-height .3s;
            flex-wrap: wrap;
        }

        .call-controls.hidden {
            opacity: 0;
            max-height: 0;
            padding: 0;
            overflow: hidden;
        }

        .call-ctrl-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .call-ctrl-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            background: rgba(255, 255, 255, .08);
            color: rgba(255, 255, 255, .5);
            transition: background .15s, color .15s, transform .1s;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .call-ctrl-btn.active {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .call-ctrl-btn.muted {
            background: rgba(229, 62, 62, .35);
            color: #fff;
        }

        .call-ctrl-btn.recording {
            background: rgba(229, 62, 62, .65);
            color: #fff;
            animation: blink 1.2s infinite;
        }

        .call-ctrl-btn:hover {
            background: rgba(255, 255, 255, .25);
            color: #fff;
            transform: scale(1.06);
        }

        .call-ctrl-btn:active {
            transform: scale(.94);
        }

        .call-ctrl-label {
            color: rgba(255, 255, 255, .45);
            font-size: .62rem;
            letter-spacing: .03em;
        }

        .device-panel {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: #111b21;
            color: #e9edef;
            border-radius: 14px 14px 0 0;
            padding: 14px;
            width: 320px;
            box-shadow: 0 -12px 40px rgba(0, 0, 0, .5);
            display: none;
            z-index: 20;
            max-width: 95vw;
        }

        .device-panel.show {
            display: block;
        }

        .device-section {
            margin-bottom: 12px;
        }

        .device-section label {
            display: block;
            font-size: .72rem;
            color: #8696a0;
            margin-bottom: 4px;
            letter-spacing: .03em;
        }

        .device-section select {
            width: 100%;
            padding: 7px 10px;
            border-radius: 8px;
            border: 1px solid #2a3942;
            background: #202c33;
            color: #e9edef;
            font-size: .8rem;
        }

        .call-minimized-pill {
            bottom: 24px;
            right: 24px;
            z-index: 89500 !important;
            background: linear-gradient(135deg, #1a202c, #2d3748);
            border-radius: 30px;
            padding: 8px 12px 8px 8px;
            display: none;
            align-items: center;
            gap: 12px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, .55), 0 0 0 1px rgba(255, 255, 255, .06);
            cursor: pointer;
            animation: pill-pop .25s cubic-bezier(.34, 1.56, .64, 1);
        }

        @keyframes pill-pop {
            from {
                transform: scale(.7);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pill-avatar-wrap {
            position: relative;
        }

        .pill-avatar-wrap img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .2);
        }

        .pill-pulse {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(104, 211, 145, .6);
            animation: audio-pulse 2s ease-in-out infinite;
        }

        .pill-info {
            color: #fff;
            min-width: 100px;
        }

        .pill-info #pill-name {
            font-size: .85rem;
            font-weight: 600;
        }

        .pill-info .pill-timer {
            font-size: .72rem;
            color: #68d391;
            letter-spacing: .05em;
        }

        .pill-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .1);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .pill-btn:hover {
            background: rgba(255, 255, 255, .25);
        }

        .pill-btn-end {
            background: rgba(229, 62, 62, .7);
        }

        .pill-btn-end:hover {
            background: rgba(229, 62, 62, .95);
        }

        .missed-call-toast {
            top: 24px;
            right: 24px;
            z-index: 99500 !important;
            background: linear-gradient(160deg, #2d1a1a, #1a0e0e);
            border: 1px solid rgba(229, 62, 62, .35);
            border-radius: 16px;
            padding: 12px 16px 12px 12px;
            display: none;
            align-items: center;
            gap: 12px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, .5);
            color: #fff;
            min-width: 340px;
            max-width: 95vw;
            animation: toast-slide .3s cubic-bezier(.34, 1.56, .64, 1);
        }

        .missed-call-toast.show {
            display: flex;
        }

        @keyframes toast-slide {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .missed-call-toast img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(229, 62, 62, .5);
        }

        .missed-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .missed-info b {
            font-size: .9rem;
        }

        .missed-info span {
            font-size: .72rem;
            color: #fc8181;
        }

        .missed-callback {
            background: rgba(56, 161, 105, .85);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 7px 12px;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .missed-callback:hover {
            background: rgba(56, 161, 105, 1);
        }

        .missed-dismiss {
            background: none;
            border: none;
            color: rgba(255, 255, 255, .5);
            cursor: pointer;
            padding: 4px;
            font-size: .8rem;
        }

        .missed-dismiss:hover {
            color: #fff;
        }

        /* ═══════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════ */

        /* TABLET: 768 – 1024px */
        @media (min-width: 768px) and (max-width: 1024px) {
            .call-window {
                width: 340px;
            }

            .call-video-area {
                height: 240px;
            }
        }

        /* ═══ MOBILE: < 768px ═══ */
        @media (max-width: 767.98px) {

            /* Overlay cards */
            .call-overlay-card {
                padding: 36px 28px 32px !important;
                min-width: 0 !important;
                max-width: calc(100vw - 32px) !important;
                border-radius: 22px !important;
                margin: 16px;
            }

            .call-avatar-lg {
                width: 88px;
                height: 88px;
            }

            .call-ring-anim {
                width: 96px;
                height: 96px;
            }

            .call-overlay-name {
                font-size: 1.2rem;
            }

            .call-overlay-actions {
                gap: 20px;
            }

            .call-action-btn {
                width: 64px;
                height: 64px;
                font-size: 1.5rem;
            }

            /* ══════════════════════════════════════════════════════
               ACTIVE CALL WINDOW — fullscreen ONLY WHEN VISIBLE
               ⚠️ We do NOT force display:flex here. The base rule
                  keeps it `display: none` until JS shows it by
                  setting style="display: block".
            ══════════════════════════════════════════════════════ */
            .call-window {
                inset: 0 !important;
                width: 100vw !important;
                height: 100dvh !important;
                max-width: none !important;
                border-radius: 0 !important;
                bottom: 0 !important;
                right: 0 !important;
                /* display stays whatever it was (none or block) */
            }

            /* When JS has made it visible, switch to a column flex layout */
            .call-window[style*="display: block"],
            .call-window[style*="display:block"],
            .call-window[style*="display: flex"],
            .call-window[style*="display:flex"] {
                display: flex !important;
                flex-direction: column;
            }

            .call-chrome {
                padding: calc(env(safe-area-inset-top) + 8px) 12px 8px 12px;
                flex-shrink: 0;
            }

            .call-chrome-title {
                font-size: .8rem;
            }

            .call-chrome-timer {
                font-size: .85rem;
            }

            .call-chrome-btn {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .call-chrome-right {
                gap: 6px;
            }

            .call-video-area {
                flex: 1 1 auto !important;
                height: auto !important;
                min-height: 0 !important;
                position: relative;
            }

            .call-active-avatar {
                width: 100px;
                height: 100px;
            }

            .call-active-name {
                font-size: 1.3rem;
                margin-top: 20px;
            }

            .call-active-sub {
                font-size: .85rem;
            }

            .local-video {
                width: 110px;
                height: 82px;
                bottom: 12px;
                right: 12px;
                border-radius: 14px;
            }

            .call-quality-badge {
                top: 12px;
                left: 12px;
                padding: 6px 10px;
            }

            .call-rec-indicator,
            .call-share-indicator {
                top: 12px;
                right: 12px;
                padding: 6px 10px;
                font-size: .72rem;
            }

            .call-controls {
                flex-shrink: 0;
                padding: 14px 10px calc(14px + env(safe-area-inset-bottom)) 10px;
                gap: 2px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .call-controls::-webkit-scrollbar {
                display: none;
            }

            .call-ctrl-group {
                flex: 0 0 auto;
                min-width: 52px;
            }

            .call-ctrl-btn {
                width: 52px;
                height: 52px;
                font-size: 1.2rem;
            }

            .call-ctrl-label {
                font-size: .68rem;
            }

            .call-controls .call-action-btn {
                width: 52px;
                height: 52px;
                font-size: 1.2rem;
            }

            .call-reaction-bar {
                bottom: calc(env(safe-area-inset-bottom) + 90px);
                padding: 8px 12px;
                gap: 6px;
            }

            .call-reaction-bar button {
                font-size: 1.8rem;
                padding: 8px 10px;
            }

            .volume-slider-wrap {
                left: 12px;
                right: 12px;
                bottom: calc(env(safe-area-inset-bottom) + 90px);
                transform: none !important;
                padding: 12px 16px;
                justify-content: space-between;
            }

            .volume-slider-wrap.show {
                transform: none !important;
            }

            .volume-slider-wrap input {
                flex: 1;
                width: auto;
                max-width: 220px;
            }

            .call-stats-panel {
                top: 12px;
                left: 12px;
                font-size: .68rem;
                padding: 8px 10px;
                min-width: 150px;
                max-width: calc(100vw - 24px);
            }

            .device-panel {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                top: auto !important;
                width: 100% !important;
                max-width: none !important;
                border-radius: 20px 20px 0 0 !important;
                padding: 20px 16px calc(20px + env(safe-area-inset-bottom)) 16px !important;
                max-height: 70dvh;
                overflow-y: auto;
                z-index: 90000 !important;
            }

            .device-section label {
                font-size: .8rem;
            }

            .device-section select {
                padding: 10px 12px;
                font-size: .9rem;
            }

            .call-minimized-pill {
                bottom: calc(env(safe-area-inset-bottom) + 16px);
                right: 16px;
                padding: 6px 10px 6px 6px;
                gap: 8px;
                max-width: calc(100vw - 32px);
            }

            .pill-avatar-wrap img {
                width: 38px;
                height: 38px;
            }

            .pill-info {
                min-width: 0;
            }

            .pill-info #pill-name {
                font-size: .8rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 90px;
            }

            .pill-btn {
                width: 32px;
                height: 32px;
            }

            .missed-call-toast {
                top: calc(env(safe-area-inset-top) + 12px);
                left: 12px;
                right: 12px;
                min-width: 0;
                padding: 10px 12px;
                gap: 10px;
            }

            .missed-call-toast img {
                width: 38px;
                height: 38px;
            }

            .missed-info b {
                font-size: .85rem;
            }

            .missed-callback {
                padding: 6px 10px;
                font-size: .72rem;
            }
        }

        /* EXTRA-SMALL: < 380px */
        @media (max-width: 379.98px) {
            .call-ctrl-btn {
                width: 46px;
                height: 46px;
                font-size: 1.05rem;
            }

            .call-ctrl-label {
                font-size: .6rem;
            }

            .call-ctrl-group {
                min-width: 46px;
            }

            .call-overlay-card {
                padding: 28px 20px 26px !important;
            }

            .call-action-btn {
                width: 56px;
                height: 56px;
                font-size: 1.3rem;
            }
        }

        /* LANDSCAPE PHONE */
        @media (max-height: 500px) and (orientation: landscape) {
            .call-video-area {
                height: auto;
            }

            .call-controls {
                padding: 8px 10px calc(8px + env(safe-area-inset-bottom)) 10px;
            }

            .call-ctrl-btn {
                width: 44px;
                height: 44px;
            }

            .call-controls .call-action-btn {
                width: 44px;
                height: 44px;
            }

            .call-ctrl-label {
                display: none;
            }

            .call-chrome {
                padding: 6px 12px;
            }

            .local-video {
                width: 90px;
                height: 68px;
            }
        }

        /* TOUCH DEVICES */
        @media (hover: none) and (pointer: coarse) {

            .call-action-btn:hover,
            .call-chrome-btn:hover,
            .call-ctrl-btn:hover,
            .pill-btn:hover,
            .call-reaction-bar button:hover {
                transform: none;
                filter: none;
                background: inherit;
            }

            .call-action-btn:active {
                transform: scale(.92);
                filter: brightness(1.1);
            }

            .call-chrome-btn:active,
            .call-ctrl-btn:active {
                background: rgba(255, 255, 255, .28);
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    @push('scripts')
        <script>
            // ══════════════════════════════════════════════════════════
            // RELOCATE CALL UI TO <body>
            // ══════════════════════════════════════════════════════════
            (function relocateCallUI() {
                var IDS = [
                    'call-outgoing-overlay',
                    'call-incoming-overlay',
                    'call-active-window',
                    'call-minimized-pill',
                    'missed-call-toast'
                ];
                function move() {
                    IDS.forEach(function (id) {
                        var el = document.getElementById(id);
                        if (el && el.parentNode !== document.body) {
                            document.body.appendChild(el);
                        }
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', move);
                } else {
                    move();
                }
                document.addEventListener('livewire:init', function () {
                    if (window.Livewire && window.Livewire.hook) {
                        Livewire.hook('morph.updated', move);
                    }
                });
                setInterval(move, 2000);
            })();

            // ══════════════════════════════════════════════════════════
            // SAFETY NET: on page load, force every call UI hidden.
            // Guards against stale state from a previous session.
            // ══════════════════════════════════════════════════════════
            (function hideAllCallUI() {
                var IDS = [
                    'call-outgoing-overlay',
                    'call-incoming-overlay',
                    'call-active-window',
                    'call-minimized-pill'
                ];
                function hide() {
                    IDS.forEach(function (id) {
                        var el = document.getElementById(id);
                        if (el) el.style.display = 'none';
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', hide);
                } else {
                    hide();
                }
            })();

            // ══════════════════════════════════════════════════════════
            // Prevent body scroll while a fullscreen mobile call is active
            // ══════════════════════════════════════════════════════════
            (function lockBodyScrollOnMobileCall() {
                var observer = new MutationObserver(function () {
                    var callWin = document.getElementById('call-active-window');
                    var outOverlay = document.getElementById('call-outgoing-overlay');
                    var inOverlay = document.getElementById('call-incoming-overlay');
                    var isMobile = window.matchMedia('(max-width: 767.98px)').matches;

                    var isVisible = function (el) {
                        if (!el) return false;
                        var d = el.style.display;
                        return d === 'flex' || d === 'block';
                    };

                    var shouldLock = isMobile && (
                        isVisible(callWin) || isVisible(outOverlay) || isVisible(inOverlay)
                    );

                    document.body.style.overflow = shouldLock ? 'hidden' : '';
                });

                observer.observe(document.body, {
                    attributes: true,
                    subtree: true,
                    attributeFilter: ['style']
                });
            })();

            // ══════════════════════════════════════════════════════════
            // RINGTONE
            // ══════════════════════════════════════════════════════════
            var _ringtoneCtx = null, _ringInterval = null, _ringtoneMode = 'default';

            function playRingtone(isIncoming) {
                stopRingtone();
                if (_ringtoneMode === 'silent') return;
                if (_ringtoneMode === 'vibrate') {
                    if (navigator.vibrate) navigator.vibrate([400, 200, 400]);
                    return;
                }
                try {
                    _ringtoneCtx = new (window.AudioContext || window.webkitAudioContext)();
                    function beep(freq, start, duration) {
                        var osc = _ringtoneCtx.createOscillator();
                        var gain = _ringtoneCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, _ringtoneCtx.currentTime + start);
                        gain.gain.setValueAtTime(0, _ringtoneCtx.currentTime + start);
                        gain.gain.linearRampToValueAtTime(0.25, _ringtoneCtx.currentTime + start + 0.02);
                        gain.gain.linearRampToValueAtTime(0.20, _ringtoneCtx.currentTime + start + duration - 0.05);
                        gain.gain.linearRampToValueAtTime(0, _ringtoneCtx.currentTime + start + duration);
                        osc.connect(gain); gain.connect(_ringtoneCtx.destination);
                        osc.start(_ringtoneCtx.currentTime + start);
                        osc.stop(_ringtoneCtx.currentTime + start + duration);
                    }
                    function ringCycle() {
                        if (!_ringtoneCtx) return;
                        if (isIncoming) { beep(480, 0, 0.4); beep(480, 0.5, 0.4); }
                        else { beep(360, 0, 0.6); }
                    }
                    ringCycle();
                    _ringInterval = setInterval(ringCycle, isIncoming ? 2000 : 2500);
                    if (isIncoming && navigator.vibrate) navigator.vibrate([300, 200, 300, 200, 300]);
                } catch (e) { console.warn('Ringtone error:', e); }
            }
            function stopRingtone() {
                if (_ringInterval) { clearInterval(_ringInterval); _ringInterval = null; }
                if (_ringtoneCtx) { try { _ringtoneCtx.close(); } catch (e) { } _ringtoneCtx = null; }
                if (navigator.vibrate) navigator.vibrate(0);
            }

            // ══════════════════════════════════════════════════════════
            // CALL MANAGER
            // ══════════════════════════════════════════════════════════
            window.CallManager = (function () {
                var ME = {{ auth()->id() }};
                var state = 'idle';
                var preMinimizeState = null;
                var peerId = null, peerName = '', peerAvatar = '';
                var callType = 'audio', callChannel = null;

                var pc = null, localStream = null, remoteStream = null;
                var iceCandidateQueue = [];
                var handlingOffer = false, offerSent = false, startingCall = false;

                var timerInterval = null, timerSeconds = 0;
                var statsInterval = null;
                var autoHideTimer = null;

                var micEnabled = true, camEnabled = true, speakerEnabled = true;
                var isRecording = false, recorder = null, recordedChunks = [];
                var isSharing = false, screenStream = null;
                var missedCall = null;

                var ICE_SERVERS = {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        { urls: 'stun:stun2.l.google.com:19302' },
                    ]
                };

                function getEl(id) { return document.getElementById(id); }
                function showEl(id, d) { var e = getEl(id); if (e) e.style.display = (d || 'flex'); }
                function hideEl(id) { var e = getEl(id); if (e) e.style.display = 'none'; }
                function csrf() { var m = document.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; }
                function postSignal(url, body) {
                    return fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                        body: JSON.stringify(body)
                    });
                }

                function cleanSdp(sdp) {
                    var raw = sdp.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
                    var lines = raw.split('\n');
                    var removePts = {}, section = null;
                    lines.forEach(function (line) {
                        var t = line.trim();
                        if (t.startsWith('m=audio')) { section = 'audio'; return; }
                        if (t.startsWith('m=video')) { section = 'video'; return; }
                        if (t.startsWith('m=')) { section = 'other'; return; }
                        var m = t.match(/^a=rtpmap:(\d+)\s+([^/\s]+)/i);
                        if (!m) return;
                        var pt = m[1], codec = m[2];
                        if (section === 'audio' && !/^(opus|isac)/i.test(codec)) removePts[pt] = true;
                        if (section === 'video' && !/^(VP8|VP9|H264)/i.test(codec)) removePts[pt] = true;
                    });
                    var cleaned = lines.filter(function (line) {
                        var t = line.trim();
                        if (!t) return false;
                        if (t.startsWith('a=ssrc:') || t.startsWith('a=ssrc-group:')) return false;
                        var rtpmap = t.match(/^a=rtpmap:(\d+)\s/);
                        if (rtpmap && removePts[rtpmap[1]]) return false;
                        var fmtp = t.match(/^a=(?:fmtp|rtcp-fb):(\d+)[\s/]/);
                        if (fmtp && removePts[fmtp[1]]) return false;
                        return true;
                    });
                    if (Object.keys(removePts).length > 0) {
                        cleaned = cleaned.map(function (line) {
                            var t = line.trim();
                            if (!t.startsWith('m=')) return line;
                            var parts = t.split(' ');
                            var header = parts.slice(0, 3);
                            var pts = parts.slice(3).filter(function (pt) { return !removePts[pt]; });
                            if (pts.length === 0) pts = ['0'];
                            return header.concat(pts).join(' ');
                        });
                    }
                    return cleaned.join('\r\n') + '\r\n';
                }
                function extractSdp(payload) {
                    if (!payload) return null;
                    if (typeof payload.sdp === 'string' && payload.sdp.trim().startsWith('{')) {
                        try { payload = JSON.parse(payload.sdp); } catch (e) { }
                    }
                    if (typeof payload.sdp === 'string') return payload.sdp;
                    if (typeof payload === 'string') {
                        try { var p = JSON.parse(payload); if (p.sdp) return p.sdp; } catch (e) { }
                    }
                    return null;
                }

                function fmt(s) { var m = String(Math.floor(s / 60)).padStart(2, '0'); var ss = String(s % 60).padStart(2, '0'); return m + ':' + ss; }
                function startTimer() {
                    timerSeconds = 0;
                    timerInterval = setInterval(function () {
                        timerSeconds++;
                        var t = fmt(timerSeconds);
                        var a = getEl('call-timer'), b = getEl('call-chrome-timer'), c = getEl('pill-timer');
                        if (a) a.textContent = t; if (b) b.textContent = t; if (c) c.textContent = t;
                    }, 1000);
                }
                function stopTimer() { clearInterval(timerInterval); timerInterval = null; }

                function subscribeCallChannel(friendId) {
                    if (!window.Echo) { setTimeout(function () { subscribeCallChannel(friendId); }, 300); return; }
                    leaveCallChannel();
                    var ids = [ME, friendId].sort(function (a, b) { return a - b; });
                    var name = 'call.' + ids[0] + '.' + ids[1];
                    callChannel = window.Echo.private(name);
                    callChannel.listen('.call.signal', function (data) {
                        if (data.type === 'call-reaction') { showFloatingEmoji(data.payload.emoji, true); return; }
                        handleSignal(data);
                    });
                }
                function leaveCallChannel() {
                    if (callChannel && window.Echo) { window.Echo.leave(callChannel.name); callChannel = null; }
                }

                function sendSignal(type, payload) {
                    postSignal('/call/signal', { to: peerId, type: type, payload: payload || {} })
                        .catch(function (e) { console.error('[CallManager] signal err:', e); });
                }

                function handleSignal(data) {
                    var from = parseInt(data.from), type = data.type, payload = data.payload || {};
                    if (from === ME) return;

                    switch (type) {
                        case 'call-request':
                            if (state !== 'idle') {
                                if (state === 'active' || state === 'outgoing') {
                                    postSignal('/call/end', { to: from, type: 'call-busy' }).catch(function () { });
                                }
                                return;
                            }
                            showIncomingCall(from, payload.callerName || 'Unknown', payload.callerAvatar || '', payload.callType || 'audio');
                            break;

                        case 'call-accepted':
                            if (state !== 'outgoing') return;
                            stopRingtone();
                            state = 'active';
                            hideEl('call-outgoing-overlay');
                            createPeerConnection(true);
                            break;

                        case 'call-declined':
                            if (state !== 'outgoing') return;
                            stopRingtone();
                            reset();
                            showToast('📵 Call declined');
                            break;

                        case 'call-busy':
                            if (state !== 'outgoing') return;
                            stopRingtone();
                            reset();
                            showToast('📵 User is busy');
                            break;

                        case 'call-ended':
                            if (state === 'idle') return;
                            stopRingtone();
                            var wasActive = (state === 'active' || state === 'minimized');
                            if (!wasActive) {
                                missedCall = { id: peerId, name: peerName, avatar: peerAvatar, type: callType };
                                showMissedCallToast();
                            }
                            reset();
                            if (wasActive) showToast('📞 Call ended');
                            break;

                        case 'offer':
                            if (state !== 'active') return;
                            var sdpO = extractSdp(payload);
                            if (sdpO) handleOffer({ type: 'offer', sdp: cleanSdp(sdpO) });
                            break;

                        case 'answer':
                            if (!pc) return;
                            var sdpA = extractSdp(payload);
                            if (!sdpA) return;
                            pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: cleanSdp(sdpA) }))
                                .then(function () {
                                    var q = iceCandidateQueue.slice(); iceCandidateQueue = [];
                                    q.forEach(function (c) { pc.addIceCandidate(new RTCIceCandidate(c)).catch(function () { }); });
                                })
                                .catch(function (e) { console.error('[CallManager] setRemote(answer):', e); });
                            break;

                        case 'ice-candidate':
                            if (!pc || !payload.candidate) return;
                            if (!pc.remoteDescription || !pc.remoteDescription.type) {
                                iceCandidateQueue.push(payload);
                                return;
                            }
                            pc.addIceCandidate(new RTCIceCandidate(payload))
                                .catch(function (e) { console.error('[CallManager] addIce:', e); });
                            break;

                        case 'screen-share-started':
                            showToast('🖥️ ' + peerName + ' is sharing their screen');
                            break;
                        case 'screen-share-stopped':
                            showToast('🖥️ ' + peerName + ' stopped sharing');
                            break;
                    }
                }

                function showIncomingCall(fromId, name, avatar, type) {
                    state = 'incoming';
                    peerId = fromId; peerName = name; peerAvatar = avatar; callType = type;
                    subscribeCallChannel(fromId);
                    getEl('call-in-avatar').src = avatar;
                    getEl('call-in-name').textContent = name;
                    getEl('call-in-type').textContent = type === 'video' ? '📹 Incoming video call' : '📞 Incoming audio call';
                    showEl('call-incoming-overlay');
                    playRingtone(true);
                    if ('Notification' in window && Notification.permission === 'granted') {
                        var n = new Notification('Incoming call', {
                            body: name + ' is calling you',
                            icon: avatar, tag: 'incoming-call', requireInteraction: true,
                        });
                        n.onclick = function () { window.focus(); n.close(); };
                    }
                }

                function startCall(type, friendId, friendName, friendAvatar) {
                    if (state !== 'idle') { showToast('Already in a call'); return; }
                    if (startingCall) return;
                    startingCall = true;
                    try {
                        state = 'outgoing';
                        peerId = friendId; peerName = friendName; peerAvatar = friendAvatar; callType = type;
                        subscribeCallChannel(friendId);
                        getEl('call-out-avatar').src = friendAvatar;
                        getEl('call-out-name').textContent = friendName;
                        getEl('call-out-type').textContent = type === 'video' ? '📹 Video call' : '📞 Audio call';
                        var ot = getEl('call-out-timer'); if (ot) ot.textContent = 'Ringing…';
                        showEl('call-outgoing-overlay');
                        playRingtone(false);
                        postSignal('/call/initiate', { to: friendId, callType: type }).catch(function () { });
                        sendSignal('call-request', {
                            callType: type,
                            callerName: '{{ addslashes(auth()->user()?->name ?? "Unknown") }}',
                            callerAvatar: '{{ auth()->user()?->getAvatarUrlAttribute() ?? "" }}'
                        });
                    } finally {
                        setTimeout(function () { startingCall = false; }, 1500);
                    }
                }

                function acceptCall(withVideo) {
                    if (state !== 'incoming') return;
                    if (withVideo) callType = 'video';
                    stopRingtone();
                    state = 'active';
                    hideEl('call-incoming-overlay');
                    sendSignal('call-accepted', {});
                    createPeerConnection(false);
                }

                function declineCall() {
                    if (state !== 'incoming') return;
                    stopRingtone();
                    var target = peerId;
                    reset();
                    if (target) postSignal('/call/end', { to: target, type: 'call-declined' }).catch(function () { });
                }

                function hangUp() {
                    if (state === 'idle') return;
                    var target = peerId;
                    stopRingtone();
                    reset();
                    if (target) postSignal('/call/end', { to: target, type: 'call-ended' }).catch(function () { });
                }

                function createPeerConnection(isOfferer) {
                    var constraints = {
                        audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                        video: callType === 'video'
                            ? { width: { ideal: 1280 }, height: { ideal: 720 }, frameRate: { ideal: 30 } }
                            : false
                    };
                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(function (stream) {
                            localStream = stream;
                            if (callType === 'video') {
                                var lv = getEl('local-video');
                                lv.srcObject = stream; lv.style.display = 'block';
                                var cw = getEl('cam-ctrl-wrap'); if (cw) cw.style.display = 'flex';
                                var cb = getEl('btn-toggle-cam'); if (cb) cb.classList.add('active');
                            }
                            getEl('call-active-avatar').src = peerAvatar;
                            getEl('call-active-name').textContent = peerName;
                            var timer0 = getEl('call-timer'); if (timer0) timer0.textContent = '00:00';
                            var avatar = getEl('remote-audio-avatar');
                            var remVid = getEl('remote-video');
                            if (avatar) avatar.style.display = 'flex';
                            if (remVid) remVid.style.display = 'none';
                            showEl('call-quality-badge', 'flex');
                            var ql = getEl('call-quality-label');
                            if (ql) ql.textContent = callType === 'video' ? 'HD' : 'Audio';
                            showEl('call-active-window', 'block');
                            var t = getEl('call-chrome-timer'); if (t) t.textContent = '00:00';
                            startTimer();
                            enumerateDevices();
                            startAutoHide();

                            pc = new RTCPeerConnection(ICE_SERVERS);
                            stream.getTracks().forEach(function (tr) { pc.addTrack(tr, stream); });
                            remoteStream = new MediaStream();
                            if (remVid) remVid.srcObject = remoteStream;

                            pc.ontrack = function (event) {
                                event.streams[0].getTracks().forEach(function (tr) { remoteStream.addTrack(tr); });
                                if (event.track.kind === 'video') {
                                    if (remVid) remVid.style.display = 'block';
                                    if (avatar) avatar.style.display = 'none';
                                }
                                if (remVid && remVid.paused) remVid.play().catch(function () { });
                            };
                            pc.onicecandidate = function (event) {
                                if (event.candidate) {
                                    sendSignal('ice-candidate', {
                                        candidate: event.candidate.candidate,
                                        sdpMid: event.candidate.sdpMid,
                                        sdpMLineIndex: event.candidate.sdpMLineIndex
                                    });
                                }
                            };
                            pc.oniceconnectionstatechange = function () {
                                updateQualityFromIce(pc.iceConnectionState);
                            };
                            pc.onconnectionstatechange = function () {
                                var dot = getEl('call-status-dot');
                                if (dot) {
                                    dot.className = 'call-status-dot';
                                    if (pc.connectionState === 'connected') dot.classList.add('ok');
                                    else if (pc.connectionState === 'connecting') dot.classList.add('warn');
                                    else if (pc.connectionState === 'failed' || pc.connectionState === 'disconnected') dot.classList.add('bad');
                                }
                                if (pc.connectionState === 'failed') { showToast('⚠️ Connection failed'); hangUp(); }
                            };

                            if (isOfferer && !offerSent) {
                                offerSent = true;
                                pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: callType === 'video' })
                                    .then(function (o) { return pc.setLocalDescription(new RTCSessionDescription({ type: 'offer', sdp: cleanSdp(o.sdp) })); })
                                    .then(function () { sendSignal('offer', { type: 'offer', sdp: pc.localDescription.sdp }); })
                                    .catch(function (e) { console.error('[CallManager] createOffer:', e); });
                            }
                        })
                        .catch(function (err) {
                            console.error('[CallManager] getUserMedia error:', err);
                            var msg = err.name === 'NotAllowedError' ? '🎤 Mic/camera permission denied' :
                                err.name === 'NotFoundError' ? '🎤 No mic/camera found' : '⚠️ Cannot access media';
                            showToast(msg); hangUp();
                        });
                }

                function handleOffer(offerData) {
                    if (!pc || handlingOffer) return;
                    handlingOffer = true;
                    pc.setRemoteDescription(new RTCSessionDescription(offerData))
                        .then(function () {
                            var q = iceCandidateQueue.slice(); iceCandidateQueue = [];
                            return Promise.all(q.map(function (c) {
                                return pc.addIceCandidate(new RTCIceCandidate(c)).catch(function () { });
                            }));
                        })
                        .then(function () { return pc.createAnswer(); })
                        .then(function (a) { return pc.setLocalDescription(new RTCSessionDescription({ type: 'answer', sdp: cleanSdp(a.sdp) })); })
                        .then(function () { sendSignal('answer', { type: 'answer', sdp: pc.localDescription.sdp }); })
                        .catch(function (e) { console.error('[CallManager] handleOffer:', e); handlingOffer = false; });
                }

                function reset() {
                    stopTimer();
                    stopStats();
                    stopAutoHide();
                    hideEl('call-outgoing-overlay');
                    hideEl('call-incoming-overlay');
                    hideEl('call-active-window');
                    hideEl('call-minimized-pill');
                    if (localStream) { localStream.getTracks().forEach(function (t) { t.stop(); }); localStream = null; }
                    if (screenStream) { screenStream.getTracks().forEach(function (t) { t.stop(); }); screenStream = null; }
                    if (pc) { try { pc.close(); } catch (e) { } pc = null; }
                    remoteStream = null;
                    var lv = getEl('local-video'), rv = getEl('remote-video'), ra = getEl('remote-audio-avatar');
                    var cw = getEl('cam-ctrl-wrap');
                    if (lv) { lv.srcObject = null; lv.style.display = 'none'; }
                    if (rv) { rv.srcObject = null; rv.style.display = 'none'; }
                    if (ra) ra.style.display = 'flex';
                    if (cw) cw.style.display = 'none';
                    hideEl('call-quality-badge'); hideEl('call-rec-indicator'); hideEl('call-share-indicator');
                    hideEl('call-stats-panel'); hideEl('device-panel');
                    var rb = getEl('call-reaction-bar'); if (rb) rb.classList.remove('show');
                    var rc = getEl('btn-record'); if (rc) rc.classList.remove('recording');
                    var rs = getEl('btn-screenshare'); if (rs) rs.classList.remove('active');
                    if (recorder && recorder.state !== 'inactive') { try { recorder.stop(); } catch (e) { } }
                    recorder = null; recordedChunks = []; isRecording = false; isSharing = false;
                    micEnabled = camEnabled = speakerEnabled = true;
                    var mb = getEl('btn-toggle-mic'), cb = getEl('btn-toggle-cam'), sb = getEl('btn-toggle-speaker');
                    if (mb) { mb.classList.add('active'); mb.classList.remove('muted'); mb.innerHTML = '<i class="bi bi-mic-fill"></i>'; }
                    if (cb) { cb.classList.add('active'); cb.classList.remove('muted'); cb.innerHTML = '<i class="bi bi-camera-video-fill"></i>'; }
                    if (sb) { sb.classList.add('active'); sb.classList.remove('muted'); sb.innerHTML = '<i class="bi bi-volume-up-fill"></i>'; }
                    var pillMic = getEl('pill-mic-btn');
                    if (pillMic) pillMic.innerHTML = '<i class="bi bi-mic-fill"></i>';
                    leaveCallChannel();
                    state = 'idle'; preMinimizeState = null;
                    peerId = null; peerName = ''; peerAvatar = '';
                    callType = 'audio'; offerSent = false; iceCandidateQueue = []; handlingOffer = false;
                    document.body.style.overflow = '';
                }

                function toggleMic() {
                    if (!localStream) return;
                    micEnabled = !micEnabled;
                    localStream.getAudioTracks().forEach(function (t) { t.enabled = micEnabled; });
                    var btn = getEl('btn-toggle-mic');
                    if (btn) {
                        btn.classList.toggle('active', micEnabled);
                        btn.classList.toggle('muted', !micEnabled);
                        btn.innerHTML = micEnabled ? '<i class="bi bi-mic-fill"></i>' : '<i class="bi bi-mic-mute-fill"></i>';
                    }
                    var pb = getEl('pill-mic-btn');
                    if (pb) pb.innerHTML = micEnabled ? '<i class="bi bi-mic-fill"></i>' : '<i class="bi bi-mic-mute-fill"></i>';
                }

                function toggleCamera() {
                    if (!localStream) return;
                    camEnabled = !camEnabled;
                    localStream.getVideoTracks().forEach(function (t) { t.enabled = camEnabled; });
                    var btn = getEl('btn-toggle-cam');
                    if (btn) {
                        btn.classList.toggle('active', camEnabled);
                        btn.classList.toggle('muted', !camEnabled);
                        btn.innerHTML = camEnabled ? '<i class="bi bi-camera-video-fill"></i>' : '<i class="bi bi-camera-video-off-fill"></i>';
                    }
                }

                function toggleSpeaker() {
                    speakerEnabled = !speakerEnabled;
                    var rv = getEl('remote-video');
                    if (rv) rv.muted = !speakerEnabled;
                    var btn = getEl('btn-toggle-speaker');
                    if (btn) {
                        btn.classList.toggle('active', speakerEnabled);
                        btn.classList.toggle('muted', !speakerEnabled);
                        btn.innerHTML = speakerEnabled ? '<i class="bi bi-volume-up-fill"></i>' : '<i class="bi bi-volume-mute-fill"></i>';
                    }
                    var vw = getEl('volume-slider-wrap');
                    if (vw) vw.classList.toggle('show', speakerEnabled);
                    setTimeout(function () { if (vw) vw.classList.remove('show'); }, 3000);
                }

                function setVolume(v) {
                    var rv = getEl('remote-video');
                    if (rv) rv.volume = v / 100;
                }

                function toggleScreenShare() {
                    if (!pc) return;
                    if (isSharing) {
                        if (screenStream) { screenStream.getTracks().forEach(function (t) { t.stop(); }); screenStream = null; }
                        var sender = pc.getSenders().find(function (s) { return s.track && s.track.kind === 'video'; });
                        if (sender && localStream) {
                            var camTrack = localStream.getVideoTracks()[0];
                            if (camTrack) sender.replaceTrack(camTrack);
                        }
                        isSharing = false;
                        var b = getEl('btn-screenshare'); if (b) b.classList.remove('active');
                        hideEl('call-share-indicator');
                        sendSignal('screen-share-stopped', {});
                        showToast('🖥️ Screen share stopped');
                        return;
                    }
                    navigator.mediaDevices.getDisplayMedia({ video: true, audio: false })
                        .then(function (stream) {
                            screenStream = stream;
                            var screenTrack = stream.getVideoTracks()[0];
                            var sender = pc.getSenders().find(function (s) { return s.track && s.track.kind === 'video'; });
                            if (sender) sender.replaceTrack(screenTrack);
                            else pc.addTrack(screenTrack, stream);
                            screenTrack.onended = function () { if (isSharing) toggleScreenShare(); };
                            isSharing = true;
                            var b = getEl('btn-screenshare'); if (b) b.classList.add('active');
                            showEl('call-share-indicator', 'flex');
                            sendSignal('screen-share-started', {});
                            showToast('🖥️ Sharing screen');
                        })
                        .catch(function (e) { console.warn('Screen share cancelled:', e); });
                }

                function togglePiP() {
                    var rv = getEl('remote-video');
                    if (!rv) return;
                    try {
                        if (document.pictureInPictureElement) document.exitPictureInPicture();
                        else if (rv.readyState >= 2) rv.requestPictureInPicture();
                        else showToast('No video to pop out');
                    } catch (e) { showToast('PiP unavailable'); }
                }

                function toggleFullscreen() {
                    var w = getEl('call-active-window');
                    if (!w) return;
                    if (!document.fullscreenElement) {
                        (w.requestFullscreen || w.webkitRequestFullscreen || function () { }).call(w);
                        w.classList.add('fullscreen');
                    } else {
                        (document.exitFullscreen || document.webkitExitFullscreen || function () { }).call(document);
                        w.classList.remove('fullscreen');
                    }
                }
                document.addEventListener('fullscreenchange', function () {
                    var w = getEl('call-active-window');
                    if (w && !document.fullscreenElement) w.classList.remove('fullscreen');
                    var b = getEl('btn-fullscreen');
                    if (b) b.innerHTML = document.fullscreenElement ? '<i class="bi bi-fullscreen-exit"></i>' : '<i class="bi bi-arrows-fullscreen"></i>';
                });

                function minimize() {
                    if (state !== 'active') return;
                    preMinimizeState = state;
                    state = 'minimized';
                    hideEl('call-active-window');
                    var p = getEl('call-minimized-pill');
                    if (p) {
                        getEl('pill-avatar').src = peerAvatar;
                        getEl('pill-name').textContent = peerName;
                        getEl('pill-timer').textContent = fmt(timerSeconds);
                        p.style.display = 'flex';
                    }
                    stopAutoHide();
                }
                function restore() {
                    if (state !== 'minimized') return;
                    state = preMinimizeState || 'active';
                    hideEl('call-minimized-pill');
                    showEl('call-active-window', 'block');
                    startAutoHide();
                }

                function toggleReactionBar() {
                    var b = getEl('call-reaction-bar');
                    if (!b) return;
                    b.classList.toggle('show');
                }
                function sendReaction(emoji) {
                    showFloatingEmoji(emoji, false);
                    sendSignal('call-reaction', { emoji: emoji });
                    var b = getEl('call-reaction-bar'); if (b) b.classList.remove('show');
                }
                function showFloatingEmoji(emoji, isRemote) {
                    var layer = getEl('call-reactions-layer');
                    if (!layer) return;
                    var el = document.createElement('div');
                    el.className = 'floating-emoji';
                    el.textContent = emoji;
                    el.style.left = (20 + Math.random() * 60) + '%';
                    layer.appendChild(el);
                    setTimeout(function () { el.remove(); }, 2700);
                }

                function startStats() {
                    if (!pc) return;
                    stopStats();
                    statsInterval = setInterval(function () {
                        if (!pc) return;
                        pc.getStats(null).then(function (stats) {
                            var inbound = null, remoteInbound = null, codec = null;
                            stats.forEach(function (s) {
                                if (s.type === 'inbound-rtp' && s.kind === 'video' && !s.isRemote) inbound = s;
                                if (s.type === 'remote-inbound-rtp') remoteInbound = s;
                                if (s.type === 'codec' && inbound && s.id === inbound.codecId) codec = s;
                            });
                            if (inbound) {
                                var bitrate = inbound.bytesReceived ? Math.round((inbound.bytesReceived * 8) / 1000) : 0;
                                var res = inbound.frameWidth && inbound.frameHeight
                                    ? inbound.frameWidth + '×' + inbound.frameHeight : '—';
                                var setEl = function (id, v) { var e = getEl(id); if (e) e.textContent = v; };
                                setEl('stat-bitrate', bitrate ? bitrate + ' kbps' : '—');
                                setEl('stat-res', res);
                                setEl('stat-loss', (inbound.packetsLost || 0) + ' pkts');
                                setEl('stat-jitter', inbound.jitter ? (inbound.jitter * 1000).toFixed(1) + ' ms' : '—');
                                setEl('stat-codec', codec ? (codec.mimeType || '').replace('video/', '') : '—');
                            }
                            if (remoteInbound) {
                                var rtt = getEl('stat-rtt');
                                if (rtt) rtt.textContent = remoteInbound.roundTripTime
                                    ? (remoteInbound.roundTripTime * 1000).toFixed(0) + ' ms' : '—';
                            }
                        }).catch(function () { });
                    }, 2000);
                }
                function stopStats() { clearInterval(statsInterval); statsInterval = null; }
                function toggleStats() {
                    var p = getEl('call-stats-panel');
                    if (!p) return;
                    if (p.style.display === 'block') { p.style.display = 'none'; stopStats(); }
                    else { p.style.display = 'block'; startStats(); }
                }

                function updateQualityFromIce(iceState) {
                    var bars = document.querySelector('#call-quality-badge .quality-bars');
                    if (!bars) return;
                    bars.className = 'quality-bars';
                    if (iceState === 'connected' || iceState === 'completed') bars.classList.add('q4');
                    else if (iceState === 'checking') bars.classList.add('q3');
                    else if (iceState === 'new') bars.classList.add('q2');
                    else bars.classList.add('q1');
                }

                function enumerateDevices() {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
                    navigator.mediaDevices.enumerateDevices().then(function (devs) {
                        var mics = [], cams = [], speakers = [];
                        devs.forEach(function (d) {
                            if (d.kind === 'audioinput') mics.push(d);
                            if (d.kind === 'videoinput') cams.push(d);
                            if (d.kind === 'audiooutput') speakers.push(d);
                        });
                        fillSelect('select-mic', mics, 'mic');
                        fillSelect('select-cam', cams, 'cam');
                        fillSelect('select-speaker', speakers, 'speaker');
                    }).catch(function () { });
                }
                function fillSelect(id, list, prefix) {
                    var s = getEl(id); if (!s) return;
                    s.innerHTML = '';
                    list.forEach(function (d, i) {
                        var opt = document.createElement('option');
                        opt.value = d.deviceId;
                        opt.textContent = d.label || (prefix + ' ' + (i + 1));
                        s.appendChild(opt);
                    });
                }
                function switchDevice(kind, deviceId) {
                    if (!localStream) return;
                    navigator.mediaDevices.getUserMedia({
                        audio: kind === 'audioinput' ? { deviceId: { exact: deviceId } } : false,
                        video: kind === 'videoinput' && callType === 'video' ? { deviceId: { exact: deviceId } } : false,
                    }).then(function (newStream) {
                        var newTrack = newStream.getTracks()[0];
                        var sender = pc.getSenders().find(function (s) { return s.track && s.track.kind === newTrack.kind; });
                        if (sender) sender.replaceTrack(newTrack);
                        localStream.getTracks().forEach(function (t) {
                            if (t.kind === newTrack.kind) { localStream.removeTrack(t); t.stop(); }
                        });
                        localStream.addTrack(newTrack);
                        if (newTrack.kind === 'video') { var lv = getEl('local-video'); if (lv) lv.srcObject = localStream; }
                        showToast('✓ Device switched');
                    }).catch(function (e) { showToast('Device switch failed'); console.warn(e); });
                }
                function toggleDevicePanel() {
                    var p = getEl('device-panel');
                    if (!p) return;
                    p.classList.toggle('show');
                    if (p.classList.contains('show')) enumerateDevices();
                }

                function setRingtoneMode(mode) {
                    _ringtoneMode = mode;
                    try { localStorage.setItem('callRingtone', mode); } catch (e) { }
                    showToast('Ringtone: ' + mode);
                }
                try {
                    var saved = localStorage.getItem('callRingtone');
                    if (saved) {
                        _ringtoneMode = saved;
                        setTimeout(function () { var s = getEl('select-ringtone'); if (s) s.value = saved; }, 100);
                    }
                } catch (e) { }

                function toggleRecording() {
                    if (isRecording) {
                        if (recorder && recorder.state !== 'inactive') recorder.stop();
                        isRecording = false;
                        var b = getEl('btn-record'); if (b) b.classList.remove('recording');
                        hideEl('call-rec-indicator');
                        showToast('⏹ Recording stopped');
                        return;
                    }
                    if (!remoteStream) { showToast('Nothing to record'); return; }
                    try {
                        recordedChunks = [];
                        var mime = MediaRecorder.isTypeSupported('video/webm;codecs=vp9,opus') ? 'video/webm;codecs=vp9,opus'
                            : MediaRecorder.isTypeSupported('video/webm') ? 'video/webm' : '';
                        recorder = new MediaRecorder(remoteStream, mime ? { mimeType: mime } : {});
                        recorder.ondataavailable = function (e) { if (e.data.size) recordedChunks.push(e.data); };
                        recorder.onstop = function () {
                            var blob = new Blob(recordedChunks, { type: mime || 'video/webm' });
                            var url = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'call_' + new Date().toISOString().replace(/[:.]/g, '-') + '.webm';
                            a.click();
                            setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
                        };
                        recorder.start();
                        isRecording = true;
                        var b = getEl('btn-record'); if (b) b.classList.add('recording');
                        showEl('call-rec-indicator', 'flex');
                        showToast('⏺ Recording started');
                    } catch (e) { showToast('Recording not supported'); console.warn(e); }
                }

                function startAutoHide() {
                    stopAutoHide();
                    var w = getEl('call-active-window');
                    var c = getEl('call-controls');
                    if (!w || !c) return;
                    if (window.matchMedia('(hover: none)').matches) return;
                    var reset = function () {
                        c.classList.remove('hidden');
                        clearTimeout(autoHideTimer);
                        autoHideTimer = setTimeout(function () {
                            if (state === 'active') c.classList.add('hidden');
                        }, 5000);
                    };
                    w.addEventListener('mousemove', reset);
                    w.addEventListener('touchstart', reset);
                    reset();
                }
                function stopAutoHide() {
                    clearTimeout(autoHideTimer); autoHideTimer = null;
                    var c = getEl('call-controls'); if (c) c.classList.remove('hidden');
                }

                function showToast(msg) {
                    var t = document.createElement('div');
                    t.textContent = msg;
                    t.style.cssText = 'position:fixed;bottom:100px;right:24px;z-index:99999;background:#1a202c;color:#fff;padding:10px 18px;border-radius:12px;font-size:.85rem;box-shadow:0 4px 20px rgba(0,0,0,.35);opacity:1;transition:opacity .4s;pointer-events:none;font-weight:500;max-width:calc(100vw - 48px);';
                    document.body.appendChild(t);
                    setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 400); }, 3000);
                }

                function showMissedCallToast() {
                    var t = getEl('missed-call-toast');
                    if (!t || !missedCall) return;
                    getEl('missed-avatar').src = missedCall.avatar;
                    getEl('missed-name').textContent = missedCall.name;
                    t.classList.add('show');
                    setTimeout(function () { t.classList.remove('show'); }, 8000);
                }
                function callbackMissed() {
                    var t = getEl('missed-call-toast'); if (t) t.classList.remove('show');
                    if (!missedCall) return;
                    startCall(missedCall.type, missedCall.id, missedCall.name, missedCall.avatar);
                }

                function initDrag() {
                    var chrome = getEl('call-chrome'), win = getEl('call-active-window');
                    if (!chrome || !win) return;
                    var isMobile = function () { return window.matchMedia('(max-width: 767.98px)').matches; };
                    var startX, startY, startLeft, startTop;

                    chrome.addEventListener('mousedown', function (e) {
                        if (isMobile()) return;
                        if (e.target.closest('button')) return;
                        var rect = win.getBoundingClientRect();
                        startX = e.clientX; startY = e.clientY;
                        startLeft = rect.left; startTop = rect.top;
                        win.classList.add('dragging', 'snapping');
                        win.style.left = rect.left + 'px';
                        win.style.top = rect.top + 'px';
                        win.style.right = 'auto';
                        win.style.bottom = 'auto';
                        function move(ev) {
                            var dx = ev.clientX - startX, dy = ev.clientY - startY;
                            win.style.left = (startLeft + dx) + 'px';
                            win.style.top = (startTop + dy) + 'px';
                        }
                        function up() {
                            document.removeEventListener('mousemove', move);
                            document.removeEventListener('mouseup', up);
                            win.classList.remove('dragging');
                            snapToCorner(win);
                        }
                        document.addEventListener('mousemove', move);
                        document.addEventListener('mouseup', up);
                    });
                }
                function snapToCorner(win) {
                    var rect = win.getBoundingClientRect();
                    var margin = 24;
                    var wW = window.innerWidth, wH = window.innerHeight;
                    var distLeft = rect.left, distRight = wW - rect.right;
                    var distTop = rect.top, distBottom = wH - rect.bottom;
                    var snapH = distLeft < distRight ? 'left' : 'right';
                    var snapV = distTop < distBottom ? 'top' : 'bottom';
                    win.style.left = win.style.right = win.style.top = win.style.bottom = 'auto';
                    if (snapH === 'left') win.style.left = margin + 'px';
                    else win.style.right = margin + 'px';
                    if (snapV === 'top') win.style.top = margin + 'px';
                    else win.style.bottom = margin + 'px';
                    setTimeout(function () { win.classList.remove('snapping'); }, 300);
                }
                setTimeout(initDrag, 100);

                document.addEventListener('keydown', function (e) {
                    if (state !== 'active' && state !== 'minimized') return;
                    var t = e.target;
                    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable)) return;
                    if (document.activeElement && document.activeElement !== document.body) {
                        var af = document.activeElement;
                        if (af.tagName === 'INPUT' || af.tagName === 'TEXTAREA' || af.tagName === 'SELECT' || af.isContentEditable) return;
                    }
                    var key = e.key.toLowerCase();
                    if (e.ctrlKey || e.metaKey || e.altKey) return;

                    switch (key) {
                        case 'm': toggleMic(); e.preventDefault(); e.stopPropagation(); break;
                        case 'v': toggleCamera(); e.preventDefault(); e.stopPropagation(); break;
                        case 's': toggleSpeaker(); e.preventDefault(); e.stopPropagation(); break;
                        case 'f': toggleFullscreen(); e.preventDefault(); e.stopPropagation(); break;
                        case 'p': togglePiP(); e.preventDefault(); e.stopPropagation(); break;
                        case 'n':
                            state === 'minimized' ? restore() : minimize();
                            e.preventDefault(); e.stopPropagation();
                            break;
                        case 'e':
                            hangUp();
                            e.preventDefault(); e.stopPropagation();
                            break;
                    }
                    if (e.key === 'Escape') {
                        var anyPicker = document.querySelector('#call-active-window .show');
                        if (!anyPicker) { hangUp(); e.preventDefault(); }
                    }
                });

                function subscribeIncomingForAll(friendIds) {
                    if (!window.Echo) { setTimeout(function () { subscribeIncomingForAll(friendIds); }, 400); return; }
                    window._callIncomingChannels = window._callIncomingChannels || {};
                    friendIds.forEach(function (friendId) {
                        var ids = [ME, friendId].sort(function (a, b) { return a - b; });
                        var name = 'call.' + ids[0] + '.' + ids[1];
                        if (window._callIncomingChannels[name]) return;
                        var ch = window.Echo.private(name);
                        ch.listen('.call.signal', function (data) {
                            if (parseInt(data.from) === ME) return;
                            if (data.type === 'call-request') handleSignal(data);
                        });
                        window._callIncomingChannels[name] = ch;
                    });
                }

                return {
                    startCall, acceptCall, declineCall, hangUp,
                    toggleMic, toggleCamera, toggleSpeaker,
                    toggleScreenShare, togglePiP, toggleFullscreen,
                    minimize, restore, toggleStats, toggleReactionBar, sendReaction,
                    toggleDevicePanel, switchDevice, setRingtoneMode, setVolume,
                    toggleRecording, callbackMissed,
                    subscribeIncomingForAll,
                };
            })();

            window.addEventListener('update-profile-subscriptions', function (e) {
                if (e.detail && e.detail.friendIds) {
                    window.CallManager.subscribeIncomingForAll(e.detail.friendIds);
                }
            });

            document.addEventListener('click', function once() {
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission();
                }
                document.removeEventListener('click', once);
            }, { once: true });
        </script>
    @endpush
</div>