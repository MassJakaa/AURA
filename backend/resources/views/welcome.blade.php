<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA — Adaptive Understanding & Responsive Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #FAF7F2;
            --surface-card: #FFFFFF;
            --surface-inner: #F6F2EB;
            --accent-lavender: #9D8CD7;
            --accent-lavender-light: #ECE7FA;
            --accent-rose: #D88A8A;
            --text-main: #2C2A36;
            --text-sub: #6C687D;
            --text-muted: #A39FAF;
            --border: #EDE6DC;
            --status-green: #52B788;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .phone-mockup {
            width: 100%;
            max-width: 410px;
            height: 820px;
            background: var(--surface-card);
            border-radius: 44px;
            box-shadow: 0 25px 60px rgba(80, 60, 120, 0.12), 0 0 0 8px #E8E2D8;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
        }

        .phone-header {
            padding: 12px 20px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            background: #FFFEFD;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .aura-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .aura-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--accent-lavender);
            background: #F2E8D9;
        }

        .aura-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 66% 22%;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--status-green);
            border-radius: 50%;
            display: inline-block;
        }

        .content-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #FAF8F5;
        }

        /* Status Card */
        .status-card {
            background: var(--surface-card);
            border-radius: 24px;
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        }

        .badge-live {
            background: var(--accent-lavender-light);
            color: #6C5AA6;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .act-name {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .act-location {
            font-size: 13px;
            color: var(--text-sub);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .time-badge {
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid #F0ECE4;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        /* Chat Messages */
        .chat-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        @keyframes bubbleIn {
            from {
                opacity: 0;
                transform: translateY(6px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .bubble {
            max-width: 82%;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 13.5px;
            line-height: 1.45;
            animation: bubbleIn 0.22s ease-out forwards;
            word-break: break-word;
        }

        .bubble-aura {
            background: var(--surface-card);
            color: var(--text-main);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .bubble-user {
            background: var(--accent-lavender);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .bubble-meta {
            font-size: 10px;
            margin-top: 4px;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        /* WhatsApp checkmarks */
        .tick-container {
            display: inline-flex;
            align-items: center;
            margin-left: 2px;
        }

        .tick-svg {
            display: inline-block;
            vertical-align: middle;
            color: rgba(255, 255, 255, 0.65);
            transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tick-blue {
            color: #53BDEB !important; /* WhatsApp iconic cyan blue tick */
            animation: tickPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes tickPop {
            0% { transform: scale(0.75); }
            50% { transform: scale(1.35); }
            100% { transform: scale(1); }
        }

        /* Reply Wrappers & Buttons */
        .bubble-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
        }

        .bubble-wrap-aura {
            justify-content: flex-start;
        }

        .bubble-wrap-user {
            justify-content: flex-end;
        }

        .reply-trigger-btn {
            opacity: 0;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: white;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .bubble-wrap:hover .reply-trigger-btn {
            opacity: 1;
        }

        .reply-trigger-btn:hover {
            background: var(--accent-lavender);
            color: white;
            border-color: var(--accent-lavender);
            transform: scale(1.1);
        }

        /* Quoted box inside message */
        .quoted-box {
            background: rgba(0, 0, 0, 0.05);
            border-left: 3.5px solid var(--accent-lavender);
            border-radius: 8px;
            padding: 5px 8px;
            margin-bottom: 6px;
            font-size: 11px;
            line-height: 1.35;
        }

        .bubble-user .quoted-box {
            background: rgba(255, 255, 255, 0.2);
            border-left-color: white;
            color: #FDFBF8;
        }

        .quoted-author {
            font-weight: 700;
            font-size: 10.5px;
            margin-bottom: 2px;
            color: var(--accent-lavender);
        }

        .bubble-user .quoted-author {
            color: #FFF;
            opacity: 0.95;
        }

        .quoted-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 230px;
            opacity: 0.9;
        }

        /* Dynamic Natural Mood Pill */
        .mood-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2.5px 8px;
            border-radius: 12px;
            background: #F4EFE6;
            border: 1px solid var(--border);
            font-size: 10px;
            font-weight: 600;
            color: var(--text-main);
            cursor: default;
            user-select: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            white-space: nowrap;
        }

        .mood-pill.pop {
            animation: moodPop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Dynamic Relationship Stage Pill */
        .stage-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2.5px 8px;
            border-radius: 12px;
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            font-size: 10px;
            font-weight: 600;
            color: #065F46;
            cursor: default;
            user-select: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            white-space: nowrap;
        }

        .stage-pill.pop {
            animation: moodPop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .header-action-btn {
            background: #F4EFE6;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5px 8px;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-sub);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .header-action-btn:hover {
            background: #EAE3D5;
            color: var(--text-main);
            border-color: #D4CBBC;
        }

        @keyframes moodPop {
            0% { transform: scale(0.92); opacity: 0.85; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Reply preview bar above input */
        .reply-preview-bar {
            padding: 8px 14px;
            background: #F8F5EE;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideUp 0.18s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(6px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .reply-bar-indicator {
            width: 3.5px;
            height: 30px;
            border-radius: 4px;
            background: var(--accent-lavender);
            flex-shrink: 0;
        }

        .reply-bar-body {
            flex: 1;
            min-width: 0;
        }

        .reply-bar-author {
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-lavender);
            margin-bottom: 2px;
        }

        .reply-bar-snippet {
            font-size: 11.5px;
            color: var(--text-sub);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reply-bar-close {
            border: none;
            background: none;
            font-size: 15px;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reply-bar-close:hover {
            color: var(--text-main);
            background: rgba(0,0,0,0.06);
        }

        /* Typing indicator */
        .typing-bubble {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 10px 16px;
            background: var(--surface-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            align-self: flex-start;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            animation: bubbleIn 0.2s ease-out forwards;
        }

        .typing-dot {
            width: 6.5px;
            height: 6.5px;
            border-radius: 50%;
            background: var(--text-muted);
            animation: typingBounce 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingBounce {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-4px);
                opacity: 1;
                background: var(--accent-lavender);
            }
        }

        /* Chat input */
        .input-bar {
            padding: 12px 16px;
            background: white;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
        }

        .input-bar input {
            flex: 1;
            padding: 12px 18px;
            border-radius: 24px;
            border: 1px solid var(--border);
            outline: none;
            font-size: 13px;
            background: #FDFBF8;
        }

        .input-bar input:focus {
            border-color: var(--accent-lavender);
        }

        .input-bar button {
            background: var(--accent-lavender);
            color: white;
            border: none;
            padding: 0 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .input-bar button:hover {
            opacity: 0.9;
        }

        .input-bar button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="phone-mockup">
    <!-- Header -->
    <div class="phone-header">
        <div class="aura-title">
            <div class="aura-avatar">
                <img src="/assets/characters/aura_master_reference.jpg" alt="AURA">
            </div>
            <div>
                <h4 style="font-size: 15px; font-weight: 700;">AURA</h4>
                <div style="font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 5px;">
                    <span class="status-dot" id="header-status-dot"></span> <span id="header-status-text">Virtual Life Online</span>
                </div>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <button type="button" class="header-action-btn" onclick="confirmResetConversation()" title="Mulai ulang percakapan dari tahap perkenalan">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    <span>Mulai Baru</span>
                </button>
                <div id="local-clock" style="font-size: 11px; font-weight: 600; color: var(--text-sub);">
                    --:-- WIB
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 4px;">
                <div class="stage-pill" id="aura-stage-pill" title="Tahap Hubungan dengan AURA">
                    <span id="aura-stage-emoji">🌱</span>
                    <span id="aura-stage-label">Baru Kenal</span>
                </div>
                <div class="mood-pill" id="aura-mood-pill" title="Suasana hati AURA saat ini">
                    <span id="aura-mood-emoji">✨</span>
                    <span id="aura-mood-label">Tenang & Santai</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrollable content -->
    <div class="content-scroll" id="content-scroll">
        <!-- Live Status Widget -->
        <div class="status-card" id="status-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="badge-live" id="badge-status">Sedang Beraktivitas</span>
            </div>
            <h3 class="act-name" id="act-title">Memuat status AURA...</h3>
            <div class="act-location">
                <span>📍</span> <span id="act-loc">Memeriksa lokasi...</span>
            </div>
            <p id="act-desc" style="font-size: 12.5px; color: var(--text-sub); line-height: 1.4; margin-bottom: 12px;"></p>
            <div class="time-badge">
                <span id="act-next">Rencana selanjutnya: ...</span>
            </div>
        </div>

        <!-- Chat bubble area -->
        <div class="chat-container" id="chat-box">
            <!-- Messages rendered dynamically from persistent SQLite database -->
        </div>
    </div>

    <!-- Reply preview bar -->
    <div class="reply-preview-bar" id="reply-preview-bar" style="display: none;">
        <div class="reply-bar-indicator"></div>
        <div class="reply-bar-body">
            <div class="reply-bar-author" id="reply-bar-author">Membalas AURA</div>
            <div class="reply-bar-snippet" id="reply-bar-snippet">...</div>
        </div>
        <button type="button" class="reply-bar-close" onclick="cancelReply()" title="Batal balas">✕</button>
    </div>

    <!-- Input bar -->
    <form class="input-bar" id="chat-form" onsubmit="handleSend(event)">
        <input type="text" id="chat-input" placeholder="Ketik pesan untuk AURA..." autocomplete="off">
        <button type="submit" id="chat-send-btn">Kirim</button>
    </form>
</div>

<script>
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function getTimeString() {
        const d = new Date();
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    function getSingleTickSvg() {
        return `<svg class="tick-svg" viewBox="0 0 16 11" width="13" height="9">
            <path d="M1 6 L5 10 L15 1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
    }

    function getDoubleTickSvg(isBlue = false) {
        return `<svg class="tick-svg ${isBlue ? 'tick-blue' : ''}" viewBox="0 0 19 11" width="15" height="9">
            <path d="M1 6 L5 10 L15 1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 6 L9 10 L19 1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
    }

    function createTypingIndicator() {
        const div = document.createElement('div');
        div.className = 'typing-bubble';
        div.id = 'typing-indicator';
        div.innerHTML = `
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        `;
        return div;
    }

    const pendingUserMessages = [];
    const conversationHistory = [];
    const unreadTickIds = [];
    let isAuraProcessing = false;
    let isAuraReading = false;
    let userIsTyping = false;
    let userTypingTimer = null;
    let silenceTimer = null;
    let readPhaseTimer = null;
    let unreadHoldTimer = null;
    let idleProactiveTimer = null;
    let hasProactedThisSession = false;
    let currentAuraMood = 'peaceful';

    // Active Reply State
    let currentReply = null;
    let activeReplyForBatch = null;

    function getMoodPacing(mood) {
        switch (mood) {
            case 'happy':
                return {
                    readDelay: 2000,        // 2 detik (excited!)
                    unreadDelay: 0,
                    minTyping: 1200,
                    charSpeed: 18,
                    idleProactiveTime: 25000 // 25 detik hening -> inisiatif chat duluan
                };
            case 'curious':
                return {
                    readDelay: 2200,        // 2.2 detik (kepo!)
                    unreadDelay: 0,
                    minTyping: 1300,
                    charSpeed: 20,
                    idleProactiveTime: 25000
                };
            case 'shy':
                return {
                    readDelay: 2500,        // 2.5 detik (salting & penasaran)
                    unreadDelay: 0,
                    minTyping: 1400,
                    charSpeed: 22,
                    idleProactiveTime: 30000
                };
            case 'concerned':
                return {
                    readDelay: 3500,        // 3.5 detik (gercep perhatian)
                    unreadDelay: 0,
                    minTyping: 1500,
                    charSpeed: 24,
                    idleProactiveTime: 0
                };
            case 'peaceful':
                return {
                    readDelay: 4500,        // 4.5 detik (santai wajar)
                    unreadDelay: 0,
                    minTyping: 1800,
                    charSpeed: 28,
                    idleProactiveTime: 0
                };
            case 'sleepy':
                return {
                    readDelay: 6500,        // 6.5 detik (ngantuk, lemes)
                    unreadDelay: 2200,      // 2.2 detik telat buka chat
                    minTyping: 2200,
                    charSpeed: 35,
                    idleProactiveTime: 0
                };
            case 'sulking':
                return {
                    readDelay: 8500,        // 8.5 detik (ngambek, sengaja lama!)
                    unreadDelay: 4500,      // 4.5 detik pura-pura belum buka chat (tetap abu-abu)
                    minTyping: 2500,
                    charSpeed: 38,
                    idleProactiveTime: 0     // cewek ngambek gak bakal chat duluan!
                };
            default:
                return {
                    readDelay: 4500,
                    unreadDelay: 0,
                    minTyping: 1800,
                    charSpeed: 28,
                    idleProactiveTime: 0
                };
        }
    }

    function setHeaderStatus(text, isTyping = false) {
        const textEl = document.getElementById('header-status-text');
        const dotEl = document.getElementById('header-status-dot');
        if (textEl) textEl.innerText = text;
        if (dotEl) {
            dotEl.style.backgroundColor = isTyping ? 'var(--accent-lavender)' : 'var(--accent-sage)';
        }
    }

    function markAllTicksAsRead() {
        unreadTickIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.dataset.read = "true";
                el.innerHTML = getDoubleTickSvg(true);
            }
        });
        unreadTickIds.length = 0;
    }

    // Reply trigger from button or double-click
    function startReply(btn) {
        const wrap = btn.closest('.bubble-wrap');
        if (!wrap) return;
        const bubble = wrap.querySelector('.bubble');
        if (bubble) startReplyFromBubble(bubble);
    }

    function startReplyFromBubble(bubble) {
        const isUser = bubble.classList.contains('bubble-user');
        const author = isUser ? 'Kamu' : 'AURA';
        const textEl = bubble.querySelector('.bubble-text-content');
        const text = textEl ? textEl.innerText.trim() : bubble.innerText.trim();

        currentReply = { sender: author, content: text };

        document.getElementById('reply-bar-author').innerText = 'Membalas ' + author;
        document.getElementById('reply-bar-snippet').innerText = text;
        document.getElementById('reply-preview-bar').style.display = 'flex';

        const input = document.getElementById('chat-input');
        input.placeholder = 'Ketik balasan untuk ' + author + '...';
        input.focus();
    }

    function cancelReply() {
        currentReply = null;
        document.getElementById('reply-preview-bar').style.display = 'none';
        const input = document.getElementById('chat-input');
        input.placeholder = 'Ketik pesan untuk AURA...';
    }

    // Listener while user is typing in the input box
    function handleInputActivity() {
        const input = document.getElementById('chat-input');
        const hasText = input.value.trim().length > 0;

        stopProactiveTimer();
        hasProactedThisSession = false;

        userIsTyping = true;
        clearTimeout(userTypingTimer);
        clearTimeout(silenceTimer);

        if (hasText) {
            // User is actively typing! Hold off AURA from reading:
            if (!isAuraProcessing) {
                if (isAuraReading) {
                    clearTimeout(readPhaseTimer);
                    clearTimeout(unreadHoldTimer);
                    isAuraReading = false;
                }
                setHeaderStatus('Virtual Life Online');
            }
        }

        userTypingTimer = setTimeout(() => {
            userIsTyping = false;
            if (pendingUserMessages.length > 0 && !isAuraProcessing && !isAuraReading) {
                checkSilenceAndRead();
            } else if (pendingUserMessages.length === 0 && !isAuraProcessing && !isAuraReading) {
                resetProactiveTimer();
            }
        }, 1100);
    }

    function checkSilenceAndRead() {
        clearTimeout(silenceTimer);
        const input = document.getElementById('chat-input');
        if (input.value.trim().length > 0 || userIsTyping) {
            return; // User is still typing
        }

        // Wait 1200ms of silence before AURA starts reading
        silenceTimer = setTimeout(() => {
            const currentVal = document.getElementById('chat-input').value.trim();
            if (currentVal.length === 0 && !userIsTyping && pendingUserMessages.length > 0 && !isAuraProcessing && !isAuraReading) {
                startReadingPhase();
            }
        }, 1200);
    }

    function startReadingPhase() {
        if (isAuraProcessing || isAuraReading || pendingUserMessages.length === 0) return;

        isAuraReading = true;
        clearTimeout(readPhaseTimer);
        clearTimeout(unreadHoldTimer);
        stopProactiveTimer();

        const pacing = getMoodPacing(currentAuraMood);

        if (pacing.unreadDelay > 0) {
            // Unread hold (e.g. sulking or sleepy): hold as grey ticks for unreadDelay ms!
            setHeaderStatus(currentAuraMood === 'sulking' ? 'AURA belum membuka chat...' : 'AURA online...');
            unreadHoldTimer = setTimeout(() => {
                if (!isAuraReading) return;
                markAllTicksAsRead();
                setHeaderStatus('Membaca pesan...');

                const remainingReadTime = Math.max(1500, pacing.readDelay - pacing.unreadDelay);
                readPhaseTimer = setTimeout(() => {
                    isAuraReading = false;
                    triggerAuraResponse();
                }, remainingReadTime);
            }, pacing.unreadDelay);
        } else {
            // Immediate read & blue ticks
            markAllTicksAsRead();
            setHeaderStatus('Membaca pesan...');

            readPhaseTimer = setTimeout(() => {
                isAuraReading = false;
                triggerAuraResponse();
            }, pacing.readDelay);
        }
    }

    // Natural Emotion / Mood Presentation
    function updateMoodDisplay(moodObj) {
        if (!moodObj) return;
        currentAuraMood = moodObj.value || 'peaceful';
        const emojiEl = document.getElementById('aura-mood-emoji');
        const labelEl = document.getElementById('aura-mood-label');
        const pillEl = document.getElementById('aura-mood-pill');

        if (emojiEl) emojiEl.innerText = moodObj.emoji || '✨';
        if (labelEl) labelEl.innerText = moodObj.label || 'Tenang & Santai';

        if (pillEl) {
            pillEl.classList.remove('pop');
            void pillEl.offsetWidth; // trigger reflow
            pillEl.classList.add('pop');
            if (moodObj.value === 'sulking') {
                pillEl.style.background = '#FEE2E2';
                pillEl.style.borderColor = '#FCA5A5';
                pillEl.style.color = '#991B1B';
            } else if (moodObj.value === 'shy') {
                pillEl.style.background = '#FCE7F3';
                pillEl.style.borderColor = '#FBCFE8';
                pillEl.style.color = '#9D174D';
            } else if (moodObj.value === 'happy') {
                pillEl.style.background = '#FEF3C7';
                pillEl.style.borderColor = '#FCD34D';
                pillEl.style.color = '#92400E';
            } else if (moodObj.value === 'concerned') {
                pillEl.style.background = '#E0F2FE';
                pillEl.style.borderColor = '#BAE6FD';
                pillEl.style.color = '#075985';
            } else if (moodObj.value === 'curious') {
                pillEl.style.background = '#EDE9FE';
                pillEl.style.borderColor = '#DDD6FE';
                pillEl.style.color = '#5B21B6';
            } else if (moodObj.value === 'sleepy') {
                pillEl.style.background = '#F1F5F9';
                pillEl.style.borderColor = '#CBD5E1';
                pillEl.style.color = '#475569';
            } else {
                pillEl.style.background = '#F4EFE6';
                pillEl.style.borderColor = 'var(--border)';
                pillEl.style.color = 'var(--text-main)';
            }
        }

        if (!isAuraProcessing && !isAuraReading && pendingUserMessages.length === 0) {
            resetProactiveTimer();
        }
    }

    // Dynamic Relationship Stage Presentation
    function updateStageDisplay(stageObj) {
        if (!stageObj) return;
        const emojiEl = document.getElementById('aura-stage-emoji');
        const labelEl = document.getElementById('aura-stage-label');
        const pillEl = document.getElementById('aura-stage-pill');

        if (emojiEl) emojiEl.innerText = stageObj.emoji || '🌱';
        if (labelEl) labelEl.innerText = stageObj.label || 'Baru Kenal';

        if (pillEl) {
            pillEl.classList.remove('pop');
            void pillEl.offsetWidth; // trigger reflow
            pillEl.classList.add('pop');

            if (stageObj.value === 'first_meeting') {
                pillEl.style.background = '#ECFDF5';
                pillEl.style.borderColor = '#A7F3D0';
                pillEl.style.color = '#065F46';
                pillEl.title = 'Tahap: Baru Kenal — AURA belum terlalu kenal kamu';
            } else if (stageObj.value === 'getting_to_know') {
                pillEl.style.background = '#E0F2FE';
                pillEl.style.borderColor = '#BAE6FD';
                pillEl.style.color = '#0369A1';
                pillEl.title = 'Tahap: Mulai Akrab — AURA sudah tau nama & mulai nyaman';
            } else if (stageObj.value === 'close_friend') {
                pillEl.style.background = '#F3E8FF';
                pillEl.style.borderColor = '#D8B4FE';
                pillEl.style.color = '#6B21A8';
                pillEl.title = 'Tahap: Teman Dekat — Obrolan santai, akrab, & natural';
            }
        }
    }

    // Load full persistent chat history from SQLite backend
    async function loadHistory() {
        try {
            const res = await fetch('/api/v1/simulator/history');
            const data = await res.json();
            if (!data.success) return;

            if (data.relationship_stage) {
                updateStageDisplay(data.relationship_stage);
            }
            if (data.current_mood) {
                updateMoodDisplay(data.current_mood);
            }

            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = '';
            conversationHistory.length = 0;

            if (Array.isArray(data.messages) && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const time = msg.time || getTimeString();
                    if (msg.sender === 'user') {
                        const replyTo = msg.metadata && msg.metadata.reply_to ? msg.metadata.reply_to : null;
                        let quotedHtml = '';
                        if (replyTo && replyTo.content) {
                            quotedHtml = `
                                <div class="quoted-box">
                                    <div class="quoted-author">${escapeHtml(replyTo.sender || 'AURA')}</div>
                                    <div class="quoted-text">${escapeHtml(replyTo.content)}</div>
                                </div>
                            `;
                        }

                        const wrap = document.createElement('div');
                        wrap.className = 'bubble-wrap bubble-wrap-user';
                        wrap.innerHTML = `
                            <button type="button" class="reply-trigger-btn" onclick="startReply(this)" title="Balas pesan">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 17 4 12 9 7"></polyline>
                                    <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                                </svg>
                            </button>
                            <div class="bubble bubble-user" ondblclick="startReplyFromBubble(this)">
                                ${quotedHtml}
                                <div class="bubble-text-content">${escapeHtml(msg.content)}</div>
                                <div class="bubble-meta">
                                    <span>${time}</span>
                                    <span class="tick-container">${getDoubleTickSvg(true)}</span>
                                </div>
                            </div>
                        `;
                        chatBox.appendChild(wrap);
                        conversationHistory.push({ role: 'user', content: msg.content });
                    } else {
                        const wrap = document.createElement('div');
                        wrap.className = 'bubble-wrap bubble-wrap-aura';
                        wrap.innerHTML = `
                            <div class="bubble bubble-aura" ondblclick="startReplyFromBubble(this)">
                                <div class="bubble-text-content">${escapeHtml(msg.content)}</div>
                                <div class="bubble-meta" style="color: var(--text-muted);">${time}</div>
                            </div>
                            <button type="button" class="reply-trigger-btn" onclick="startReply(this)" title="Balas pesan">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 17 4 12 9 7"></polyline>
                                    <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                                </svg>
                            </button>
                        `;
                        chatBox.appendChild(wrap);
                        conversationHistory.push({ role: 'model', content: msg.content });
                    }
                });
            } else {
                chatBox.innerHTML = `
                    <div id="empty-chat-hint" style="text-align: center; margin: 35px auto 20px auto; color: var(--text-muted); font-size: 12px; line-height: 1.6; max-width: 260px; background: rgba(255,255,255,0.75); padding: 14px 18px; border-radius: 18px; border: 1px dashed var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <div style="font-size: 22px; margin-bottom: 6px;">🌱</div>
                        <b style="color: var(--text-main); font-size: 13px;">Tahap Perkenalan Awal</b><br>
                        Kirim sapaan pertama kamu (misal: <i>"Halo Aura"</i> atau <i>"Hai!"</i>) untuk mulai berkenalan dengan AURA ✨
                    </div>
                `;
            }

            const scroll = document.getElementById('content-scroll');
            if (scroll) {
                setTimeout(() => {
                    scroll.scrollTop = scroll.scrollHeight;
                }, 50);
            }
        } catch (e) {
            console.error('Failed to load history:', e);
        }
    }

    // Reset conversation back to first meeting
    async function confirmResetConversation() {
        if (!confirm("Mulai percakapan baru dari awal?\\n\\nRiwayat chat saat ini akan diarsipkan dan kamu bisa menyapa AURA untuk berkenalan dari awal (🌱 Baru Kenal).")) {
            return;
        }

        try {
            const res = await fetch('/api/v1/simulator/reset', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });
            const data = await res.json();
            if (data.success) {
                cancelReply();
                pendingUserMessages.length = 0;
                unreadTickIds.length = 0;
                clearTimeout(silenceTimer);
                clearTimeout(readPhaseTimer);
                clearTimeout(unreadHoldTimer);
                stopProactiveTimer();
                isAuraProcessing = false;
                isAuraReading = false;
                hasProactedThisSession = false;
                setHeaderStatus('Virtual Life Online');

                await loadHistory();
            }
        } catch (e) {
            console.error('Reset conversation error:', e);
        }
    }

    async function loadStatus() {
        try {
            const res = await fetch('/api/v1/simulator/status');
            const json = await res.json();
            if (json.success && json.data) {
                if (json.data.current_mood) {
                    updateMoodDisplay(json.data.current_mood);
                }
                const cur = json.data.current_activity;
                const charTime = new Date(json.data.virtual_time.character_local);
                
                document.getElementById('local-clock').innerText = charTime.getHours().toString().padStart(2, '0') + ':' + charTime.getMinutes().toString().padStart(2, '0') + ' WIB';

                if (cur) {
                    document.getElementById('act-title').innerText = cur.name;
                    document.getElementById('act-loc').innerText = (cur.location ? cur.location.name : 'Kamar AURA');
                    document.getElementById('act-desc').innerText = cur.description || 'Sedang menikmati rutinitas harian dengan tenang.';
                    document.getElementById('badge-status').innerText = (cur.mood_presentation ? 'Mood: ' + cur.mood_presentation : 'Aktif');
                    
                    if (json.data.next_planned_activity) {
                        document.getElementById('act-next').innerText = 'Selanjutnya: ' + json.data.next_planned_activity.name;
                    }
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function handleSend(e) {
        e.preventDefault();

        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        // Remove empty state hint if present
        const emptyHint = document.getElementById('empty-chat-hint');
        if (emptyHint) {
            emptyHint.remove();
        }

        // Check if there is an active quoted reply
        let quotedHtml = '';
        if (currentReply) {
            quotedHtml = `
                <div class="quoted-box">
                    <div class="quoted-author">${escapeHtml(currentReply.sender)}</div>
                    <div class="quoted-text">${escapeHtml(currentReply.content)}</div>
                </div>
            `;
            activeReplyForBatch = { ...currentReply };
            cancelReply();
        }

        // Create user bubble wrapped in bubble-wrap
        const tickId = 'tick-' + Date.now() + '-' + Math.random().toString(36).substr(2, 4);
        const wrap = document.createElement('div');
        wrap.className = 'bubble-wrap bubble-wrap-user';
        wrap.innerHTML = `
            <button type="button" class="reply-trigger-btn" onclick="startReply(this)" title="Balas pesan">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 17 4 12 9 7"></polyline>
                    <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                </svg>
            </button>
            <div class="bubble bubble-user" ondblclick="startReplyFromBubble(this)">
                ${quotedHtml}
                <div class="bubble-text-content">${escapeHtml(text)}</div>
                <div class="bubble-meta">
                    <span>${getTimeString()}</span>
                    <span class="tick-container" id="${tickId}">${getSingleTickSvg()}</span>
                </div>
            </div>
        `;
        chatBox.appendChild(wrap);
        unreadTickIds.push(tickId);

        // After 350ms, single tick becomes double grey tick (delivered)
        setTimeout(() => {
            const el = document.getElementById(tickId);
            if (el && !el.dataset.read) {
                el.innerHTML = getDoubleTickSvg(false);
            }
        }, 350);

        input.value = '';
        input.focus();
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        pendingUserMessages.push(text);
        conversationHistory.push({ role: 'user', content: text });

        // Pause reading until user silence is confirmed
        stopProactiveTimer();
        hasProactedThisSession = false;
        clearTimeout(unreadHoldTimer);
        clearTimeout(readPhaseTimer);
        isAuraReading = false;
        checkSilenceAndRead();
    }

    async function triggerAuraResponse() {
        if (isAuraProcessing || pendingUserMessages.length === 0) return;

        isAuraProcessing = true;
        isAuraReading = false;
        clearTimeout(readPhaseTimer);
        clearTimeout(unreadHoldTimer);
        stopProactiveTimer();
        setHeaderStatus('AURA sedang mengetik...', true);

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        // Remove empty state hint if present
        const emptyHint = document.getElementById('empty-chat-hint');
        if (emptyHint) {
            emptyHint.remove();
        }

        // Show typing indicator
        const typingIndicator = createTypingIndicator();
        chatBox.appendChild(typingIndicator);
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        // Snapshot current batch & reply context
        const batch = [...pendingUserMessages];
        const replyContextToSend = activeReplyForBatch ? { ...activeReplyForBatch } : null;
        pendingUserMessages.length = 0;
        activeReplyForBatch = null;

        const startTime = Date.now();
        let replies = [];

        try {
            const res = await fetch('/api/v1/simulator/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    messages: batch,
                    reply_to: replyContextToSend,
                    history: conversationHistory.slice(-8)
                })
            });
            const data = await res.json();
            if (data.current_mood) {
                updateMoodDisplay(data.current_mood);
            }
            if (data.relationship_stage) {
                updateStageDisplay(data.relationship_stage);
            }
            if (data.replies && Array.isArray(data.replies) && data.replies.length > 0) {
                replies = data.replies;
            } else if (data.reply) {
                replies = [data.reply];
            } else {
                replies = ['Terima kasih sudah berbagi cerita sama aku ✨'];
            }
        } catch (err) {
            replies = ['Terima kasih sudah nemenin malam ini 🌿', 'Ada cerita seru apa lagi hari ini?'];
        }

        // Realistic typing duration driven by mood
        const pacing = getMoodPacing(currentAuraMood);
        const elapsed = Date.now() - startTime;
        if (elapsed < pacing.minTyping) {
            await sleep(pacing.minTyping - elapsed);
        }

        // Sequentially deliver each bubble with a typing pause
        for (let i = 0; i < replies.length; i++) {
            const bubbleText = replies[i];

            if (i > 0) {
                chatBox.appendChild(typingIndicator);
                scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
                const typingDuration = Math.min(2400, Math.max(900, bubbleText.length * pacing.charSpeed));
                await sleep(typingDuration);
            }

            if (typingIndicator.parentNode) {
                typingIndicator.parentNode.removeChild(typingIndicator);
            }

            const wrap = document.createElement('div');
            wrap.className = 'bubble-wrap bubble-wrap-aura';
            wrap.innerHTML = `
                <div class="bubble bubble-aura" ondblclick="startReplyFromBubble(this)">
                    <div class="bubble-text-content">${escapeHtml(bubbleText)}</div>
                    <div class="bubble-meta" style="color: var(--text-muted);">${getTimeString()}</div>
                </div>
                <button type="button" class="reply-trigger-btn" onclick="startReply(this)" title="Balas pesan">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 17 4 12 9 7"></polyline>
                        <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                    </svg>
                </button>
            `;
            chatBox.appendChild(wrap);
            conversationHistory.push({ role: 'model', content: bubbleText });
            scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

            if (i < replies.length - 1) {
                await sleep(350);
            }
        }

        isAuraProcessing = false;
        setHeaderStatus('Virtual Life Online');

        if (pendingUserMessages.length > 0) {
            checkSilenceAndRead();
        } else {
            resetProactiveTimer();
        }
    }

    // Proactive Chat Initiation when user is idle & AURA is excited
    function resetProactiveTimer() {
        stopProactiveTimer();
        if (hasProactedThisSession) return;
        if (isAuraProcessing || isAuraReading) return;
        if (pendingUserMessages.length > 0) return;
        if (conversationHistory.length === 0) return; // Menunggu sapaan pertama dari pengguna

        const input = document.getElementById('chat-input');
        if (input && input.value.trim().length > 0) return;

        const pacing = getMoodPacing(currentAuraMood);
        if (pacing.idleProactiveTime > 0) {
            idleProactiveTimer = setTimeout(() => {
                triggerProactiveChat();
            }, pacing.idleProactiveTime);
        }
    }

    function stopProactiveTimer() {
        if (idleProactiveTimer) {
            clearTimeout(idleProactiveTimer);
            idleProactiveTimer = null;
        }
    }

    async function triggerProactiveChat() {
        const input = document.getElementById('chat-input');
        if (input && input.value.trim().length > 0) return;
        if (userIsTyping || isAuraProcessing || isAuraReading || pendingUserMessages.length > 0) return;
        if (hasProactedThisSession) return;

        hasProactedThisSession = true;
        isAuraProcessing = true;
        setHeaderStatus('AURA sedang mengetik...', true);

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        // Remove empty state hint if present
        const emptyHint = document.getElementById('empty-chat-hint');
        if (emptyHint) {
            emptyHint.remove();
        }

        const typingIndicator = createTypingIndicator();
        chatBox.appendChild(typingIndicator);
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        let replies = [];

        try {
            const res = await fetch('/api/v1/simulator/proactive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    history: conversationHistory.slice(-6)
                })
            });
            const data = await res.json();
            if (data.current_mood) {
                updateMoodDisplay(data.current_mood);
            }
            if (data.relationship_stage) {
                updateStageDisplay(data.relationship_stage);
            }
            if (data.replies && Array.isArray(data.replies) && data.replies.length > 0) {
                replies = data.replies;
            } else if (data.reply) {
                replies = [data.reply];
            } else {
                replies = ['Eh kok ngilang sih? wkwk', 'Lagi sibuk yaa?'];
            }
        } catch (err) {
            replies = ['Eh masih ada orangnya gak nih? 🙈', 'Jangan-jangan ketiduran yaa wkwk'];
        }

        const pacing = getMoodPacing(currentAuraMood);
        await sleep(Math.max(1200, pacing.minTyping));

        for (let i = 0; i < replies.length; i++) {
            const bubbleText = replies[i];

            if (i > 0) {
                chatBox.appendChild(typingIndicator);
                scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
                const typingDuration = Math.min(2200, Math.max(900, bubbleText.length * pacing.charSpeed));
                await sleep(typingDuration);
            }

            if (typingIndicator.parentNode) {
                typingIndicator.parentNode.removeChild(typingIndicator);
            }

            const wrap = document.createElement('div');
            wrap.className = 'bubble-wrap bubble-wrap-aura';
            wrap.innerHTML = `
                <div class="bubble bubble-aura" ondblclick="startReplyFromBubble(this)">
                    <div class="bubble-text-content">${escapeHtml(bubbleText)}</div>
                    <div class="bubble-meta" style="color: var(--text-muted);">${getTimeString()}</div>
                </div>
                <button type="button" class="reply-trigger-btn" onclick="startReply(this)" title="Balas pesan">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 17 4 12 9 7"></polyline>
                        <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                    </svg>
                </button>
            `;
            chatBox.appendChild(wrap);
            conversationHistory.push({ role: 'model', content: bubbleText });
            scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

            if (i < replies.length - 1) {
                await sleep(350);
            }
        }

        isAuraProcessing = false;
        setHeaderStatus('Virtual Life Online');
    }

    const inputEl = document.getElementById('chat-input');
    inputEl.addEventListener('input', handleInputActivity);
    inputEl.addEventListener('keydown', handleInputActivity);

    function updateClockNow() {
        const d = new Date();
        const clockEl = document.getElementById('local-clock');
        if (clockEl) {
            clockEl.innerText = d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0') + ' WIB';
        }
    }

    updateClockNow();
    setInterval(updateClockNow, 10000);
    loadStatus();
    setInterval(loadStatus, 30000);
    loadHistory();
</script>

</body>
</html>
