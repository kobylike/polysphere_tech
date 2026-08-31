<div>
    {{-- ══ OUTGOING CALL OVERLAY ══ --}}
    <div id="call-outgoing-overlay"
        style="display: none; position: fixed; inset: 0; z-index: 9900; background: rgba(10,10,30,0.88); backdrop-filter: blur(8px); align-items: center; justify-content: center;">
        <div class="call-overlay-card">
            <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                <div class="call-ring-anim"></div>
                <div class="call-ring-anim" style="animation-delay: 0.5s;"></div>
                <img id="call-out-avatar" src="" alt=""
                    style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; border: 3px solid rgba(255,255,255,0.25);">
            </div>
            <div id="call-out-name" style="font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 6px;"></div>
            <div id="call-out-type" style="font-size: 0.85rem; color: #a0aec0; margin-bottom: 32px;"></div>
            <div style="display: flex; justify-content: center;">
                <button onclick="window.CallManager.hangUp()" class="call-action-btn call-end-btn" title="Cancel call">
                    <i class="bi bi-telephone-x-fill"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ INCOMING CALL OVERLAY ══ --}}
    <div id="call-incoming-overlay"
        style="display: none; position: fixed; inset: 0; z-index: 9900; background: rgba(10,10,30,0.88); backdrop-filter: blur(8px); align-items: center; justify-content: center;">
        <div class="call-overlay-card">
            <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                <div class="call-ring-anim"></div>
                <div class="call-ring-anim" style="animation-delay: 0.5s;"></div>
                <img id="call-in-avatar" src="" alt=""
                    style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; position: relative; z-index: 1; border: 3px solid rgba(255,255,255,0.25);">
            </div>
            <div id="call-in-name" style="font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 6px;"></div>
            <div id="call-in-type" style="font-size: 0.85rem; color: #a0aec0; margin-bottom: 32px;"></div>
            <div style="display: flex; justify-content: center; gap: 28px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                    <button onclick="window.CallManager.acceptCall()" class="call-action-btn call-accept-btn"
                        title="Accept">
                        <i class="bi bi-telephone-fill"></i>
                    </button>
                    <span style="color: #a0aec0; font-size: 0.75rem;">Accept</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                    <button onclick="window.CallManager.declineCall()" class="call-action-btn call-end-btn"
                        title="Decline">
                        <i class="bi bi-telephone-x-fill"></i>
                    </button>
                    <span style="color: #a0aec0; font-size: 0.75rem;">Decline</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ ACTIVE CALL WINDOW ══ --}}
    <div id="call-active-window"
        style="display: none; position: fixed; bottom: 24px; right: 24px; z-index: 8900; width: 340px; border-radius: 22px; overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,0.6); background: #0d1117;">

        {{-- Remote area --}}
        <div id="call-video-area"
            style="position: relative; background: #161b22; height: 230px; display: flex; align-items: center; justify-content: center;">
            <video id="remote-video" autoplay playsinline
                style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; border-radius: 0;"></video>

            <div id="remote-audio-avatar" style="text-align: center; z-index: 1; position: relative;">
                <div style="position: relative; display: inline-block;">
                    <div id="call-avatar-ring"
                        style="position: absolute; inset: -8px; border-radius: 50%; border: 2px solid rgba(99,179,237,0.4); animation: audio-pulse 2s ease-in-out infinite;">
                    </div>
                    <img id="call-active-avatar" src="" alt=""
                        style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
                </div>
                <div id="call-active-name" style="color: #fff; font-weight: 600; font-size: 1rem; margin-top: 12px;">
                </div>
                <div id="call-timer" style="color: #68d391; font-size: 0.82rem; margin-top: 4px;">00:00</div>
            </div>

            {{-- Local video PIP --}}
            <video id="local-video" autoplay muted playsinline
                style="position: absolute; bottom: 10px; right: 10px; width: 82px; height: 62px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(255,255,255,0.25); display: none; z-index: 2;"></video>

            {{-- Quality indicator --}}
            <div id="call-quality-badge"
                style="position: absolute; top: 10px; left: 10px; z-index: 2; background: rgba(0,0,0,0.5); border-radius: 12px; padding: 3px 8px; display: none; align-items: center; gap: 4px;">
                <div id="call-quality-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #68d391;">
                </div>
                <span id="call-quality-label" style="color: #fff; font-size: 0.7rem;">HD</span>
            </div>
        </div>

        {{-- Controls --}}
        <div
            style="background: #0d1117; padding: 14px 20px 18px; display: flex; justify-content: space-around; align-items: center;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <button id="btn-toggle-mic" onclick="window.CallManager.toggleMic()" class="call-ctrl-btn active"
                    title="Mute/Unmute">
                    <i class="bi bi-mic-fill"></i>
                </button>
                <span class="call-ctrl-label">Mute</span>
            </div>
            <div id="cam-ctrl-wrap"
                style="display: flex; flex-direction: column; align-items: center; gap: 6px; display: none;">
                <button id="btn-toggle-cam" onclick="window.CallManager.toggleCamera()" class="call-ctrl-btn active"
                    title="Camera on/off">
                    <i class="bi bi-camera-video-fill"></i>
                </button>
                <span class="call-ctrl-label">Camera</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <button id="btn-toggle-speaker" onclick="window.CallManager.toggleSpeaker()"
                    class="call-ctrl-btn active" title="Speaker">
                    <i class="bi bi-volume-up-fill"></i>
                </button>
                <span class="call-ctrl-label">Speaker</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <button onclick="window.CallManager.hangUp()" class="call-action-btn call-end-btn"
                    style="width: 50px; height: 50px; font-size: 1.1rem;" title="End call">
                    <i class="bi bi-telephone-x-fill"></i>
                </button>
                <span class="call-ctrl-label">End</span>
            </div>
        </div>
    </div>

    {{-- ══ STYLES ══ --}}
    <style>
        .call-overlay-card {
            background: linear-gradient(160deg, #1a1a2e, #16213e);
            border-radius: 26px;
            padding: 44px 56px 40px;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6);
            min-width: 300px;
            color: #fff;
        }

        .call-ring-anim {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 2px solid rgba(99, 179, 237, 0.5);
            animation: ring-pulse 1.6s ease-out infinite;
        }

        @keyframes ring-pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: .8;
            }

            100% {
                transform: translate(-50%, -50%) scale(2.1);
                opacity: 0;
            }
        }

        @keyframes audio-pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: .4;
            }

            50% {
                transform: scale(1.15);
                opacity: .8;
            }
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
            font-size: 1.3rem;
            transition: transform .15s, filter .15s;
        }

        .call-action-btn:hover {
            transform: scale(1.08);
            filter: brightness(1.15);
        }

        .call-accept-btn {
            background: linear-gradient(135deg, #38a169, #276749);
            color: #fff;
            box-shadow: 0 6px 20px rgba(56, 161, 105, 0.5);
        }

        .call-end-btn {
            background: linear-gradient(135deg, #e53e3e, #9b2c2c);
            color: #fff;
            box-shadow: 0 6px 20px rgba(229, 62, 62, 0.5);
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
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.45);
            transition: background .15s, color .15s, transform .1s;
        }

        .call-ctrl-btn.active {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .call-ctrl-btn.muted {
            background: rgba(229, 62, 62, 0.35);
            color: #fff;
        }

        .call-ctrl-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            transform: scale(1.05);
        }

        .call-ctrl-label {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.65rem;
            letter-spacing: .03em;
        }
    </style>

    {{-- ══ BOOTSTRAP ICONS (if not already included) ══ --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- ══ CALL MANAGER JAVASCRIPT ══ --}}
    @push('scripts')
        <script>
            // ============================================================
            // RINGTONE
            // ============================================================
            var _ringtoneCtx = null;
            var _ringInterval = null;

            function playRingtone(isIncoming) {
                stopRingtone();
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
                        osc.connect(gain);
                        gain.connect(_ringtoneCtx.destination);
                        osc.start(_ringtoneCtx.currentTime + start);
                        osc.stop(_ringtoneCtx.currentTime + start + duration);
                    }
                    function ringCycle() {
                        if (!_ringtoneCtx) return;
                        if (isIncoming) {
                            beep(480, 0, 0.4);
                            beep(480, 0.5, 0.4);
                        } else {
                            beep(360, 0, 0.6);
                        }
                    }
                    ringCycle();
                    _ringInterval = setInterval(ringCycle, isIncoming ? 2000 : 2500);
                } catch (e) { console.warn('Ringtone error:', e); }
            }

            function stopRingtone() {
                if (_ringInterval) { clearInterval(_ringInterval); _ringInterval = null; }
                if (_ringtoneCtx) { try { _ringtoneCtx.close(); } catch (e) { } _ringtoneCtx = null; }
            }

            // ============================================================
            // CALL MANAGER – WebRTC + signalling
            // ============================================================
            window.CallManager = (function () {
                var ME = {{ auth()->id() }};
                var state = 'idle';  // idle | outgoing | incoming | active
                var peerId = null;
                var peerName = '';
                var peerAvatar = '';
                var callType = 'audio';
                var callChannel = null;

                var pc = null;
                var localStream = null;
                var remoteStream = null;
                var iceCandidateQueue = [];
                var handlingOffer = false;
                var offerSent = false;
                var startingCall = false;

                var timerInterval = null;
                var timerSeconds = 0;
                var micEnabled = true;
                var camEnabled = true;
                var speakerEnabled = true;

                var ICE_SERVERS = {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        { urls: 'stun:stun2.l.google.com:19302' },
                    ]
                };

                function cleanSdp(sdp) {
                    var raw = sdp.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
                    var lines = raw.split('\n');
                    var removePts = {};
                    var section = null;
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

                function showEl(id) { var e = document.getElementById(id); if (e) e.style.display = 'flex'; }
                function hideEl(id) { var e = document.getElementById(id); if (e) e.style.display = 'none'; }
                function getEl(id) { return document.getElementById(id); }

                function csrfToken() {
                    var m = document.querySelector('meta[name="csrf-token"]');
                    return m ? m.getAttribute('content') : '';
                }
                function postSignal(url, body) {
                    return fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                        body: JSON.stringify(body)
                    });
                }

                function startTimer() {
                    timerSeconds = 0;
                    timerInterval = setInterval(function () {
                        timerSeconds++;
                        var m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
                        var s = String(timerSeconds % 60).padStart(2, '0');
                        var te = getEl('call-timer');
                        if (te) te.textContent = m + ':' + s;
                    }, 1000);
                }
                function stopTimer() { clearInterval(timerInterval); timerInterval = null; }

                function subscribeCallChannel(friendId) {
                    if (!window.Echo) { setTimeout(function () { subscribeCallChannel(friendId); }, 300); return; }
                    leaveCallChannel();
                    var ids = [ME, friendId].sort(function (a, b) { return a - b; });
                    var name = 'call.' + ids[0] + '.' + ids[1];
                    console.log('[CallManager] Subscribe call channel:', name);
                    callChannel = window.Echo.private(name);
                    callChannel.listen('.call.signal', function (data) { handleSignal(data); });
                }
                function leaveCallChannel() {
                    if (callChannel && window.Echo) { window.Echo.leave(callChannel.name); callChannel = null; }
                }

                function sendSignal(type, payload) {
                    console.log('[CallManager] sendSignal:', type, 'to', peerId);
                    postSignal('/call/signal', { to: peerId, type: type, payload: payload || {} })
                        .catch(function (e) { console.error('[CallManager] Signal error:', e); });
                }

                function handleSignal(data) {
                    var from = parseInt(data.from);
                    var type = data.type;
                    var payload = data.payload || {};
                    console.log('[CallManager] Signal:', type, 'from', from, 'state:', state);
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
                            console.log('[CallManager] Call accepted – creating offer');
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
                            var wasActive = (state === 'active');
                            reset();
                            showToast(wasActive ? '📞 Call ended' : '📵 Missed call');
                            break;
                        case 'offer':
                            if (state !== 'active') return;
                            var sdp = extractSdp(payload);
                            if (!sdp) { console.error('[CallManager] offer: no SDP'); return; }
                            handleOffer({ type: 'offer', sdp: cleanSdp(sdp) });
                            break;
                        case 'answer':
                            if (!pc) return;
                            var sdp = extractSdp(payload);
                            if (!sdp) { console.error('[CallManager] answer: no SDP'); return; }
                            pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: cleanSdp(sdp) }))
                                .then(function () {
                                    console.log('[CallManager] Remote description set (answer). Draining', iceCandidateQueue.length, 'ICE candidates');
                                    var q = iceCandidateQueue.slice(); iceCandidateQueue = [];
                                    q.forEach(function (c) {
                                        pc.addIceCandidate(new RTCIceCandidate(c)).catch(function (e) { console.warn('[CallManager] ICE drain:', e); });
                                    });
                                })
                                .catch(function (e) { console.error('[CallManager] setRemoteDesc(answer):', e); });
                            break;
                        case 'ice-candidate':
                            if (!pc || !payload.candidate) return;
                            if (!pc.remoteDescription || !pc.remoteDescription.type) {
                                console.log('[CallManager] Buffer ICE (no remoteDescription yet)');
                                iceCandidateQueue.push(payload);
                                return;
                            }
                            pc.addIceCandidate(new RTCIceCandidate(payload))
                                .catch(function (e) { console.error('[CallManager] addIceCandidate:', e); });
                            break;
                    }
                }

                function showIncomingCall(fromId, name, avatar, type) {
                    console.log('[CallManager] Incoming call from', fromId, type);
                    state = 'incoming';
                    peerId = fromId;
                    peerName = name;
                    peerAvatar = avatar;
                    callType = type;
                    subscribeCallChannel(fromId);
                    getEl('call-in-avatar').src = avatar;
                    getEl('call-in-name').textContent = name;
                    getEl('call-in-type').textContent = type === 'video' ? '📹 Incoming video call' : '📞 Incoming audio call';
                    showEl('call-incoming-overlay');
                    playRingtone(true);
                }

                function startCall(type, friendId, friendName, friendAvatar) {
                    if (state !== 'idle') { showToast('Already in a call'); return; }
                    if (startingCall) { return; }
                    startingCall = true;
                    try {
                        console.log('[CallManager] Starting call to', friendId, type);
                        state = 'outgoing';
                        peerId = friendId;
                        peerName = friendName;
                        peerAvatar = friendAvatar;
                        callType = type;
                        subscribeCallChannel(friendId);
                        getEl('call-out-avatar').src = friendAvatar;
                        getEl('call-out-name').textContent = friendName;
                        getEl('call-out-type').textContent = type === 'video' ? '📹 Calling…' : '📞 Calling…';
                        showEl('call-outgoing-overlay');
                        playRingtone(false);
                        postSignal('/call/initiate', { to: friendId, callType: type })
                            .catch(function (e) { console.error('[CallManager] initiate:', e); });
                        sendSignal('call-request', {
                            callType: type,
                            callerName: '{{ addslashes(auth()->user()->name) }}',
                            callerAvatar: '{{ auth()->user()->getAvatarUrlAttribute() }}'
                        });
                    } finally {
                        setTimeout(function () { startingCall = false; }, 1500);
                    }
                }

                function acceptCall() {
                    if (state !== 'incoming') return;
                    console.log('[CallManager] Accepting call');
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
                    if (target) {
                        postSignal('/call/end', { to: target, type: 'call-declined' })
                            .catch(function (e) { console.error('[CallManager] decline:', e); });
                    }
                }

                function hangUp() {
                    if (state === 'idle') return;
                    console.log('[CallManager] Hang up, state:', state);
                    var target = peerId;
                    stopRingtone();
                    reset();
                    if (target) {
                        postSignal('/call/end', { to: target, type: 'call-ended' })
                            .catch(function (e) { console.error('[CallManager] hangup signal:', e); });
                    }
                }

                function createPeerConnection(isOfferer) {
                    console.log('[CallManager] createPeerConnection, isOfferer:', isOfferer, 'type:', callType);
                    var constraints = {
                        audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                        video: callType === 'video' ? { width: { ideal: 1280 }, height: { ideal: 720 }, frameRate: { ideal: 30 } } : false
                    };
                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(function (stream) {
                            localStream = stream;
                            console.log('[CallManager] Got local stream');
                            if (callType === 'video') {
                                var lv = getEl('local-video');
                                lv.srcObject = stream;
                                lv.style.display = 'block';
                                var cw = getEl('cam-ctrl-wrap');
                                if (cw) cw.style.display = 'flex';
                                var cb = getEl('btn-toggle-cam');
                                if (cb) cb.classList.add('active');
                            }
                            getEl('call-active-avatar').src = peerAvatar;
                            getEl('call-active-name').textContent = peerName;
                            getEl('call-timer').textContent = '00:00';
                            var avatar = getEl('remote-audio-avatar');
                            var remVid = getEl('remote-video');
                            if (avatar) avatar.style.display = 'flex';
                            if (remVid) remVid.style.display = 'none';
                            var qb = getEl('call-quality-badge');
                            if (qb) { qb.style.display = 'flex'; }
                            var ql = getEl('call-quality-label');
                            if (ql) ql.textContent = callType === 'video' ? 'HD' : 'Audio';
                            showEl('call-active-window');
                            startTimer();
                            pc = new RTCPeerConnection(ICE_SERVERS);
                            stream.getTracks().forEach(function (track) { pc.addTrack(track, stream); });
                            remoteStream = new MediaStream();
                            if (remVid) remVid.srcObject = remoteStream;
                            pc.ontrack = function (event) {
                                console.log('[CallManager] Remote track:', event.track.kind);
                                event.streams[0].getTracks().forEach(function (t) { remoteStream.addTrack(t); });
                                if (event.track.kind === 'video') {
                                    if (remVid) remVid.style.display = 'block';
                                    if (avatar) avatar.style.display = 'none';
                                    if (qb) qb.style.display = 'flex';
                                }
                                if (remVid && remVid.paused) remVid.play().catch(function (e) { console.warn('Remote video play:', e); });
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
                            pc.oniceconnectionstatechange = function () { console.log('[CallManager] ICE state:', pc.iceConnectionState); };
                            pc.onconnectionstatechange = function () {
                                console.log('[CallManager] Connection state:', pc.connectionState);
                                if (pc.connectionState === 'failed') { showToast('⚠️ Connection failed'); hangUp(); }
                                var dot = getEl('call-quality-dot');
                                if (dot) {
                                    dot.style.background = pc.connectionState === 'connected' ? '#68d391'
                                        : pc.connectionState === 'connecting' ? '#f6e05e' : '#fc8181';
                                }
                            };
                            if (isOfferer) {
                                if (offerSent) return;
                                offerSent = true;
                                pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: callType === 'video' })
                                    .then(function (offer) { return pc.setLocalDescription(new RTCSessionDescription({ type: 'offer', sdp: cleanSdp(offer.sdp) })); })
                                    .then(function () { sendSignal('offer', { type: 'offer', sdp: pc.localDescription.sdp }); })
                                    .catch(function (e) { console.error('[CallManager] createOffer:', e); });
                            }
                        })
                        .catch(function (err) {
                            console.error('[CallManager] getUserMedia error:', err);
                            var msg = err.name === 'NotAllowedError' ? '🎤 Mic/camera permission denied' :
                                err.name === 'NotFoundError' ? '🎤 No mic/camera found' : '⚠️ Cannot access media devices';
                            showToast(msg);
                            hangUp();
                        });
                }

                function handleOffer(offerData) {
                    if (!pc) return;
                    if (handlingOffer) return;
                    handlingOffer = true;
                    console.log('[CallManager] Handling offer');
                    pc.setRemoteDescription(new RTCSessionDescription(offerData))
                        .then(function () {
                            var q = iceCandidateQueue.slice(); iceCandidateQueue = [];
                            return Promise.all(q.map(function (c) { return pc.addIceCandidate(new RTCIceCandidate(c)).catch(function (e) { console.warn('[CallManager] ICE drain (offer):', e); }); }));
                        })
                        .then(function () { return pc.createAnswer(); })
                        .then(function (answer) { return pc.setLocalDescription(new RTCSessionDescription({ type: 'answer', sdp: cleanSdp(answer.sdp) })); })
                        .then(function () { sendSignal('answer', { type: 'answer', sdp: pc.localDescription.sdp }); })
                        .catch(function (e) { console.error('[CallManager] handleOffer:', e); handlingOffer = false; });
                }

                function reset() {
                    console.log('[CallManager] Reset');
                    stopTimer();
                    hideEl('call-outgoing-overlay');
                    hideEl('call-incoming-overlay');
                    hideEl('call-active-window');
                    if (localStream) { localStream.getTracks().forEach(function (t) { t.stop(); }); localStream = null; }
                    if (pc) { try { pc.close(); } catch (e) { } pc = null; }
                    remoteStream = null;
                    var lv = getEl('local-video');
                    var rv = getEl('remote-video');
                    var ra = getEl('remote-audio-avatar');
                    var cw = getEl('cam-ctrl-wrap');
                    var qb = getEl('call-quality-badge');
                    if (lv) { lv.srcObject = null; lv.style.display = 'none'; }
                    if (rv) { rv.srcObject = null; rv.style.display = 'none'; }
                    if (ra) { ra.style.display = 'flex'; }
                    if (cw) { cw.style.display = 'none'; }
                    if (qb) { qb.style.display = 'none'; }
                    micEnabled = true; camEnabled = true; speakerEnabled = true;
                    var mb = getEl('btn-toggle-mic');
                    var cb = getEl('btn-toggle-cam');
                    var sb = getEl('btn-toggle-speaker');
                    if (mb) { mb.classList.add('active'); mb.classList.remove('muted'); mb.innerHTML = '<i class="bi bi-mic-fill"></i>'; }
                    if (cb) { cb.classList.remove('active', 'muted'); }
                    if (sb) { sb.classList.add('active'); }
                    leaveCallChannel();
                    state = 'idle';
                    peerId = null;
                    peerName = '';
                    peerAvatar = '';
                    callType = 'audio';
                    offerSent = false;
                    iceCandidateQueue = [];
                    handlingOffer = false;
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
                }

                function showToast(msg) {
                    var t = document.createElement('div');
                    t.textContent = msg;
                    t.style.cssText = 'position:fixed;bottom:100px;right:24px;z-index:99999;background:#1a202c;color:#fff;padding:10px 18px;border-radius:12px;font-size:0.85rem;box-shadow:0 4px 20px rgba(0,0,0,0.35);opacity:1;transition:opacity .4s;pointer-events:none;font-weight:500;';
                    document.body.appendChild(t);
                    setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 400); }, 3000);
                }

                function subscribeIncomingForAll(friendIds) {
                    if (!window.Echo) { setTimeout(function () { subscribeIncomingForAll(friendIds); }, 400); return; }
                    window._callIncomingChannels = window._callIncomingChannels || {};
                    friendIds.forEach(function (friendId) {
                        var ids = [ME, friendId].sort(function (a, b) { return a - b; });
                        var name = 'call.' + ids[0] + '.' + ids[1];
                        if (window._callIncomingChannels[name]) return;
                        console.log('[CallManager] Global subscription:', name);
                        var ch = window.Echo.private(name);
                        ch.listen('.call.signal', function (data) {
                            if (parseInt(data.from) === ME) return;
                            if (data.type !== 'call-request') return;
                            handleSignal(data);
                        });
                        window._callIncomingChannels[name] = ch;
                    });
                }

                return {
                    startCall: startCall,
                    acceptCall: acceptCall,
                    declineCall: declineCall,
                    hangUp: hangUp,
                    toggleMic: toggleMic,
                    toggleCamera: toggleCamera,
                    toggleSpeaker: toggleSpeaker,
                    subscribeIncomingForAll: subscribeIncomingForAll,
                };
            })();

            window.addEventListener('update-profile-subscriptions', function (e) {
                if (e.detail && e.detail.friendIds) {
                    window.CallManager.subscribeIncomingForAll(e.detail.friendIds);
                }
            });
        </script>
    @endpush
</div>