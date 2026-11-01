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
            padding: 16px 24px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            background: #FFFEFD;
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
            opacity: 0.65;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
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
        <div id="local-clock" style="font-size: 12px; font-weight: 600; color: var(--text-sub);">
            --:-- WIB
        </div>
    </div>

    <!-- Scrollable content -->
    <div class="content-scroll" id="content-scroll">
        <!-- Live Status Widget -->
        <div class="status-card" id="status-box">
            <span class="badge-live" id="badge-status">Sedang Beraktivitas</span>
            <h3 class="act-name" id="act-title">Memuat status AURA...</h3>
            <div class="act-location">
                <span>📍</span> <span id="act-loc">Memeriksa lokasi...</span>
            </div>
            <p id="act-desc" style="font-size: 12.5px; color: var(--text-sub); line-height: 1.4; margin-bottom: 12px;"></p>
            <div class="time-badge">
                <span id="act-next">Rencana selanjutnya: ...</span>
                <span id="act-mood">Mood: ...</span>
            </div>
        </div>

        <!-- Chat bubble area -->
        <div class="chat-container" id="chat-box">
            <div class="bubble bubble-aura">
                Halo! Senang bisa menyapamu malam ini. Aku sedang berada di kamarku bersiap istirahat. Ada cerita apa hari ini? 🌿
            </div>
        </div>
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

    async function loadStatus() {
        try {
            const res = await fetch('/api/v1/simulator/status');
            const json = await res.json();
            if (json.success && json.data) {
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

    const pendingUserMessages = [];
    const conversationHistory = [];
    let readTimeout = null;
    let isAuraProcessing = false;

    function setHeaderStatus(text, isTyping = false) {
        const textEl = document.getElementById('header-status-text');
        const dotEl = document.getElementById('header-status-dot');
        if (textEl) textEl.innerText = text;
        if (dotEl) {
            dotEl.style.backgroundColor = isTyping ? 'var(--accent-lavender)' : 'var(--accent-sage)';
        }
    }

    async function handleSend(e) {
        e.preventDefault();

        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        // 1. Immediately render User Bubble
        const userBubble = document.createElement('div');
        userBubble.className = 'bubble bubble-user';
        userBubble.innerHTML = `
            <div>${escapeHtml(text)}</div>
            <div class="bubble-meta">${getTimeString()} · ✓✓</div>
        `;
        chatBox.appendChild(userBubble);
        input.value = '';
        input.focus(); // Keep input enabled and focused so user can type multiple messages
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        // Enqueue message
        pendingUserMessages.push(text);
        conversationHistory.push({ role: 'user', content: text });

        // If AURA is not actively rendering bubbles, manage the reading window
        if (!isAuraProcessing) {
            setHeaderStatus('Membaca pesan...');

            // Extend/reset reading timer when user sends consecutive messages
            if (readTimeout) {
                clearTimeout(readTimeout);
            }

            // Longer read delay: 3.2 seconds
            readTimeout = setTimeout(() => {
                triggerAuraResponse();
            }, 3200);
        }
    }

    async function triggerAuraResponse() {
        if (isAuraProcessing || pendingUserMessages.length === 0) return;

        isAuraProcessing = true;
        readTimeout = null;
        setHeaderStatus('AURA sedang mengetik...', true);

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        // Show typing indicator
        const typingIndicator = createTypingIndicator();
        chatBox.appendChild(typingIndicator);
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        // Take snapshot of pending messages to respond to as one holistic thought
        const batch = [...pendingUserMessages];
        pendingUserMessages.length = 0;

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
                    history: conversationHistory.slice(-8)
                })
            });
            const data = await res.json();
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

        // Realistic typing duration (at least 1.8 seconds)
        const elapsed = Date.now() - startTime;
        if (elapsed < 1800) {
            await sleep(1800 - elapsed);
        }

        // Sequentially deliver each bubble with a typing pause
        for (let i = 0; i < replies.length; i++) {
            const bubbleText = replies[i];

            // If not first bubble, show typing indicator for the subsequent bubble
            if (i > 0) {
                chatBox.appendChild(typingIndicator);
                scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
                const typingDuration = Math.min(2400, Math.max(1100, bubbleText.length * 28));
                await sleep(typingDuration);
            }

            // Remove typing indicator before appending bubble
            if (typingIndicator.parentNode) {
                typingIndicator.parentNode.removeChild(typingIndicator);
            }

            const auraBubble = document.createElement('div');
            auraBubble.className = 'bubble bubble-aura';
            auraBubble.innerHTML = `
                <div>${escapeHtml(bubbleText)}</div>
                <div class="bubble-meta" style="color: var(--text-muted);">${getTimeString()}</div>
            `;
            chatBox.appendChild(auraBubble);
            conversationHistory.push({ role: 'model', content: bubbleText });
            scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

            // Small pause between sending one message and starting to type the next
            if (i < replies.length - 1) {
                await sleep(450);
            }
        }

        isAuraProcessing = false;
        setHeaderStatus('Virtual Life Online');

        // If the user sent more messages WHILE AURA was delivering her response, trigger another reading cycle
        if (pendingUserMessages.length > 0) {
            setHeaderStatus('Membaca pesan...');
            readTimeout = setTimeout(() => {
                triggerAuraResponse();
            }, 2500);
        }
    }

    loadStatus();
    setInterval(loadStatus, 30000);
</script>

</body>
</html>
