<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#FAF7F2">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>AURA — Virtual Life Companion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            background-color: var(--bg-cream);
            color: var(--text-main);
            height: 100%;
            height: 100dvh;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Responsive Main Web Application Layout */
        .app-layout {
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            display: flex;
            overflow: hidden;
            position: relative;
            background: var(--bg-cream);
        }

        /* Left Sidebar: AURA Virtual Life Hub */
        .app-sidebar {
            width: 380px;
            min-width: 340px;
            max-width: 400px;
            background: var(--surface-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: 100%;
            z-index: 50;
            box-shadow: 2px 0 20px rgba(80, 60, 120, 0.03);
            flex-shrink: 0;
        }

        .sidebar-top {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FFFEFD;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-sparkle {
            font-size: 19px;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.3px;
        }

        .brand-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-left: 2px;
            padding-left: 8px;
            border-left: 1px solid var(--border);
        }

        .sidebar-close-btn {
            display: none;
            background: none;
            border: none;
            font-size: 17px;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .character-profile-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: #FAF8F4;
            border: 1px solid var(--border);
            border-radius: 20px;
        }

        .character-avatar-wrap {
            position: relative;
            width: 62px;
            height: 62px;
            flex-shrink: 0;
        }

        .character-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            object-position: 48% 30%;
            border: 2.5px solid var(--accent-lavender);
            background: #F2E8D9;
        }

        .online-indicator-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 13px;
            height: 13px;
            background-color: var(--status-green);
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 0 6px rgba(82, 183, 136, 0.6);
        }

        .character-info {
            flex: 1;
            min-width: 0;
        }

        .character-name {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0 0 2px 0;
        }

        .character-tagline {
            font-size: 11px;
            color: var(--text-sub);
            margin-bottom: 8px;
        }

        .character-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .stage-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }

        .mood-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            background: var(--accent-lavender-light);
            border: 1px solid #DDD6FE;
            color: #5B21B6;
            transition: all 0.3s;
        }

        /* Live Status Card */
        .status-card {
            background: var(--surface-card);
            border-radius: 20px;
            padding: 16px 18px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
        }

        .badge-live {
            background: var(--accent-lavender-light);
            color: #6C5AA6;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 10px;
            display: inline-block;
            letter-spacing: 0.3px;
        }

        .clock-chip {
            font-size: 11px;
            font-weight: 700;
            color: #6C5AA6;
            background: #FAF7F2;
            border: 1px solid #EFEAE0;
            padding: 3px 8px;
            border-radius: 8px;
        }

        .act-name {
            font-size: 15.5px;
            font-weight: 700;
            margin: 8px 0 4px 0;
            color: var(--text-main);
        }

        .act-location {
            font-size: 12.5px;
            color: var(--text-sub);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }

        .time-badge {
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid #F0ECE4;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        /* Active Scenario Box in Sidebar */
        .sidebar-scenario-box {
            background: #FFFDF9;
            border: 1px solid #EFE8DC;
            border-radius: 18px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-scenario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-scenario-kicker {
            font-size: 10px;
            font-weight: 800;
            color: #8C8275;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .sidebar-story-link {
            background: none;
            border: none;
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-lavender);
            cursor: pointer;
            padding: 2px 4px;
        }

        .sidebar-story-link:hover {
            text-decoration: underline;
        }

        .sidebar-scenario-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
        }

        .sidebar-scenario-premise {
            font-size: 11.5px;
            color: var(--text-sub);
            line-height: 1.45;
            margin: 0;
        }

        .sidebar-footer {
            padding: 14px 20px 16px;
            border-top: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #FFFEFD;
        }

        .sidebar-action-btn {
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .sidebar-action-btn.primary {
            background: var(--accent-lavender);
            color: white;
            border: none;
            box-shadow: 0 2px 8px rgba(157, 140, 215, 0.3);
        }

        .sidebar-action-btn.primary:hover {
            background: #8B78C9;
        }

        .sidebar-action-btn.secondary {
            background: #FAF8F4;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .sidebar-action-btn.secondary:hover {
            background: #F0ECE4;
        }

        .sidebar-caption {
            font-size: 10px;
            color: var(--text-muted);
            text-align: center;
            margin-top: 2px;
        }

        /* Right / Main Chat Area */
        .app-main-chat {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #FAF8F5;
            position: relative;
        }

        .chat-header {
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 254, 253, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 20;
            height: 64px;
        }

        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            padding: 6px;
            color: var(--text-main);
            cursor: pointer;
            border-radius: 8px;
        }

        .chat-header-avatar {
            position: relative;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }

        .chat-header-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            object-position: 48% 30%;
            border: 2px solid var(--accent-lavender);
            background: #F2E8D9;
        }

        .header-avatar-dot {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background-color: var(--status-green);
            border: 1.5px solid white;
            border-radius: 50%;
        }

        .chat-header-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }

        .online-tag {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--status-green);
            background: #E8F5E9;
            padding: 1px 6px;
            border-radius: 6px;
        }

        .chat-header-activity {
            font-size: 11px;
            color: var(--text-sub);
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--status-green);
            border-radius: 50%;
            display: inline-block;
        }

        .chat-header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-action-btn {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 6px 12px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-main);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .header-action-btn:hover {
            background: #F4EFE6;
            border-color: var(--accent-lavender);
            transform: translateY(-1px);
        }

        /* Message Scroll Stream */
        .content-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 24px 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #FAF8F5;
        }

        .chat-container {
            max-width: 840px;
            width: 100%;
            margin: 0 auto;
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
            max-width: 80%;
            padding: 11px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
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
            box-shadow: 0 2px 8px rgba(157, 140, 215, 0.25);
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
            color: #53BDEB !important;
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: rgba(0,0,0,0.05);
            color: var(--text-sub);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .bubble-wrap:hover .reply-trigger-btn {
            opacity: 1;
        }

        .reply-trigger-btn:hover {
            background: var(--accent-lavender);
            color: white;
            transform: scale(1.1);
        }

        /* Quoted Bubble Box */
        .quoted-box {
            background: rgba(0, 0, 0, 0.05);
            border-left: 3.5px solid var(--accent-lavender);
            padding: 6px 10px;
            border-radius: 6px;
            margin-bottom: 6px;
            font-size: 11.5px;
            max-width: 100%;
        }

        .bubble-user .quoted-box {
            background: rgba(0, 0, 0, 0.15);
            border-left-color: white;
        }

        .quoted-author {
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 10.5px;
            color: var(--accent-lavender);
        }

        .bubble-user .quoted-author {
            color: rgba(255, 255, 255, 0.9);
        }

        .quoted-text {
            color: var(--text-sub);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .bubble-user .quoted-text {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Reply preview bar above input */
        .reply-preview-bar {
            max-width: 840px;
            margin: 0 auto;
            width: 100%;
            padding: 8px 18px;
            background: #F8F5EE;
            border-top: 1px solid var(--border);
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideUp 0.18s ease-out;
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

        /* Chat Input Bar */
        .input-bar-container {
            background: #FFFFFF;
            border-top: 1px solid var(--border);
            padding: 14px 24px;
            padding-bottom: max(14px, env(safe-area-inset-bottom));
        }

        .input-bar {
            max-width: 840px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-bar input {
            flex: 1;
            padding: 13px 22px;
            border-radius: 28px;
            border: 1px solid var(--border);
            outline: none;
            font-size: 14px;
            background: #FDFBF8;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .input-bar input:focus {
            border-color: var(--accent-lavender);
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(157, 140, 215, 0.15);
        }

        .input-bar button {
            background: var(--accent-lavender);
            color: white;
            border: none;
            padding: 12px 22px;
            border-radius: 24px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(157, 140, 215, 0.3);
            flex-shrink: 0;
        }

        .input-bar button:hover {
            background: #8977C6;
            transform: translateY(-1px);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(28, 25, 38, 0.45);
            backdrop-filter: blur(2px);
            z-index: 990;
        }

        /* Modal Overlay & Dialogs */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(28, 25, 38, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease-out;
            padding: 16px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(12px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-card {
            background: white;
            width: 100%;
            max-width: 480px;
            max-height: 88vh;
            border-radius: 24px;
            padding: 22px 20px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(44, 42, 54, 0.2);
            animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid var(--border);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: var(--text-muted);
            padding: 6px;
            border-radius: 8px;
        }

        .modal-close:hover {
            color: var(--text-main);
            background: #F4EFE6;
        }

        .scenario-list {
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 9px;
            padding-right: 2px;
            max-height: 380px;
        }

        .scenario-item-btn {
            background: #FAF8F5;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 14px;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .scenario-item-btn:hover {
            background: #F4EFE6;
            border-color: var(--accent-lavender);
            transform: translateY(-1px);
        }

        .scenario-item-btn.random-btn {
            background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
            border-color: #DDD6FE;
        }

        .scenario-badge {
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .badge-initiator-aura {
            background: #EDE9FE;
            color: #5B21B6;
            border: 1px solid #DDD6FE;
        }

        .badge-initiator-user {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        /* Narrative Prologue Modal */
        .narrative-card {
            background: #FFFFFF;
            max-height: 90vh;
            border: 1px solid #EDE6DC;
            padding: 22px 20px 20px;
            position: relative;
        }

        .narrative-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .narrative-badge-kicker {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #F7F4EE;
            border: 1px solid #EAE2D5;
            color: #7A7265;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 8px;
        }

        .narrative-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .narrative-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #F5F0FF, #EDE6FB);
            border: 1px solid #E0D4F7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(157, 140, 215, 0.15);
        }

        .narrative-title-group {
            flex: 1;
            min-width: 0;
        }

        .narrative-title-group h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.3;
        }

        .narrative-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-sub);
        }

        .narrative-scroll-area {
            overflow-y: auto;
            max-height: 380px;
            padding-right: 2px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
        }

        .narrative-story-box {
            background: linear-gradient(180deg, #FDFCF9 0%, #F9F5EC 100%);
            border: 1px solid #ECE2D2;
            border-radius: 16px;
            padding: 14px 16px;
            position: relative;
            box-shadow: inset 0 1px 2px rgba(255,255,255,0.8), 0 2px 6px rgba(0,0,0,0.02);
        }

        .narrative-quote-decor {
            position: absolute;
            top: 6px;
            right: 12px;
            font-size: 46px;
            line-height: 1;
            font-family: Georgia, serif;
            color: rgba(157, 140, 215, 0.18);
            pointer-events: none;
            user-select: none;
        }

        .narrative-body-text {
            font-size: 13px;
            line-height: 1.6;
            color: #383445;
            position: relative;
            z-index: 1;
        }

        .narrative-body-text p {
            margin-bottom: 10px;
        }

        .narrative-body-text p:last-child {
            margin-bottom: 0;
        }

        .narrative-action-box {
            background: #F5F3FF;
            border: 1px solid #DDD6FE;
            border-radius: 13px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .narrative-action-icon {
            font-size: 16px;
            flex-shrink: 0;
        }

        .narrative-action-text {
            font-size: 11.5px;
            color: #5B21B6;
            font-weight: 600;
            line-height: 1.4;
        }

        .narrative-footer {
            margin-top: 4px;
        }

        .narrative-start-btn {
            width: 100%;
            padding: 12px 18px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent-lavender), #7C68BF);
            color: white;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(108, 90, 166, 0.28);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .narrative-start-btn:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 6px 18px rgba(108, 90, 166, 0.35);
        }

        .narrative-start-btn:active {
            transform: scale(0.985);
        }

        /* Responsive Breakpoints */
        @media (max-width: 880px) {
            .app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 84%;
                max-width: 350px;
                background: #FFFFFF;
                z-index: 1000;
                box-shadow: 6px 0 30px rgba(0,0,0,0.18);
                transform: translateX(-105%);
                transition: transform 0.26s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .app-sidebar.open {
                transform: translateX(0);
            }

            .sidebar-backdrop.open {
                display: block;
            }

            .sidebar-close-btn {
                display: block;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .chat-header {
                padding: 10px 14px;
                height: 58px;
            }

            .content-scroll {
                padding: 14px 12px;
            }

            .input-bar-container {
                padding: 10px 14px;
                padding-bottom: max(10px, env(safe-area-inset-bottom));
            }

            .input-bar input {
                padding: 11px 16px;
                font-size: 13px;
            }

            .input-bar button {
                padding: 11px 18px;
            }
        }

        @media (max-width: 600px) {
            .modal-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .modal-card {
                max-width: 100%;
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
                max-height: 90dvh;
            }
        }
    </style>
</head>
<body>

<div class="app-layout">
    <!-- Mobile Backdrop for Sidebar Drawer -->
    <div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

    <!-- Left Sidebar: AURA Virtual Life Hub & Profile -->
    <aside class="app-sidebar" id="app-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <span class="brand-sparkle">✨</span>
                <span class="brand-name">AURA</span>
                <span class="brand-sub">Virtual Life Companion</span>
            </div>
            <button type="button" class="sidebar-close-btn" onclick="toggleSidebar(false)" title="Tutup Menu">✕</button>
        </div>

        <div class="sidebar-scroll">
            <!-- AURA Character Card -->
            <div class="character-profile-card">
                <div class="character-avatar-wrap">
                    <img src="/assets/characters/aura_profile.png?v=2" alt="AURA" class="character-avatar-img">
                    <span class="online-indicator-dot" id="sidebar-status-dot" title="AURA Sedang Online"></span>
                </div>
                <div class="character-info">
                    <h3 class="character-name">AURA</h3>
                    <p class="character-tagline">Teman Virtual Hidup Nyata</p>
                    <div class="character-badges">
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

            <!-- Virtual Life Status Card -->
            <div class="status-card" id="status-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span class="badge-live" id="badge-status">Sedang Beraktivitas</span>
                    <span class="clock-chip" id="local-clock">--:-- WIB</span>
                </div>
                <h4 class="act-name" id="act-title">Memuat status AURA...</h4>
                <div class="act-location">
                    <span>📍</span> <span id="act-loc">Memeriksa lokasi...</span>
                </div>
                <p id="act-desc" style="font-size: 12.5px; color: var(--text-sub); line-height: 1.45; margin-bottom: 12px;"></p>
                <div class="time-badge">
                    <span id="act-next">Rencana selanjutnya: ...</span>
                </div>
            </div>

            <!-- Active Scenario Info Card in Sidebar -->
            <div class="sidebar-scenario-box" id="sidebar-scenario-box">
                <div class="sidebar-scenario-header">
                    <span class="sidebar-scenario-kicker">SKENARIO AKTIF</span>
                    <button type="button" class="sidebar-story-link" onclick="openCurrentNarrativeModal()">📖 Baca Kisah</button>
                </div>
                <div class="sidebar-scenario-title" id="sidebar-scenario-title">🌱 Perkenalan Santai</div>
                <p class="sidebar-scenario-premise" id="sidebar-scenario-premise">Memuat skenario...</p>
            </div>
        </div>

        <div class="sidebar-footer">
            <button type="button" class="sidebar-action-btn primary" onclick="openScenarioModal()">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                </svg>
                <span>Mulai Obrolan Baru</span>
            </button>
            <button type="button" class="sidebar-action-btn secondary" onclick="openCurrentNarrativeModal()">
                <span>📖 Prolog Skenario</span>
            </button>
            <div class="sidebar-caption">
                <span>Powered by Gemini 2.5 • Continuous Life Engine</span>
            </div>
        </div>
    </aside>

    <!-- Main Chat Work Area -->
    <main class="app-main-chat">
        <!-- Chat Header -->
        <header class="chat-header">
            <div class="chat-header-left" onclick="toggleSidebar(true)">
                <button type="button" class="mobile-menu-btn" onclick="event.stopPropagation(); toggleSidebar(true)" title="Buka Profil & Status AURA">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="chat-header-avatar">
                    <img src="/assets/characters/aura_profile.png?v=2" alt="AURA">
                    <span class="header-avatar-dot"></span>
                </div>
                <div class="chat-header-titles">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <h2 class="chat-header-name">AURA</h2>
                        <span class="online-tag">Online</span>
                    </div>
                    <div class="chat-header-activity" id="chat-header-activity">
                        <span id="header-status-dot" class="status-dot"></span>
                        <span id="header-status-text">Virtual Life Online</span>
                    </div>
                </div>
            </div>

            <div class="chat-header-right">
                <button type="button" class="header-action-btn" onclick="openCurrentNarrativeModal()" title="Lihat Cerita Prolog Skenario" id="header-story-btn">
                    <span style="font-size: 12px;">📖</span>
                    <span>Kisah</span>
                </button>
                <button type="button" class="header-action-btn" onclick="openScenarioModal()" title="Mulai obrolan baru dengan skenario pilihan">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    <span>Mulai Baru</span>
                </button>
            </div>
        </header>

        <!-- Chat Message Scroll Stream -->
        <div class="content-scroll" id="content-scroll">
            <div class="chat-container" id="chat-box">
                <!-- Messages rendered dynamically from persistent SQLite database -->
            </div>
        </div>

        <!-- Reply Preview Bar -->
        <div class="reply-preview-bar" id="reply-preview-bar" style="display: none;">
            <div class="reply-bar-indicator"></div>
            <div class="reply-bar-body">
                <div class="reply-bar-author" id="reply-bar-author">Membalas AURA</div>
                <div class="reply-bar-snippet" id="reply-bar-snippet">...</div>
            </div>
            <button type="button" class="reply-bar-close" onclick="cancelReply()" title="Batal balas">✕</button>
        </div>

        <!-- Chat Input Bar -->
        <div class="input-bar-container">
            <form class="input-bar" id="chat-form" onsubmit="handleSend(event)">
                <input type="text" id="chat-input" placeholder="Ketik pesan untuk AURA..." autocomplete="off">
                <button type="submit" id="chat-send-btn">
                    <span>Kirim</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>
    </main>

    <!-- Scenario Selection Modal -->
    <div class="modal-overlay" id="scenario-modal" style="display: none;" onclick="if(event.target === this) closeScenarioModal()">
        <div class="modal-card">
            <div class="modal-header">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0;">Mulai Obrolan Baru</h4>
                <button type="button" class="modal-close" onclick="closeScenarioModal()">✕</button>
            </div>
            <p style="font-size: 12px; color: var(--text-sub); margin: 6px 0 14px 0; line-height: 1.4;">
                Pilih skenario bagaimana kamu dan AURA bisa saling kenal & punya kontak:
            </p>
            <div class="scenario-list" id="scenario-list">
                <!-- Dynamically loaded from /api/v1/simulator/scenarios -->
            </div>
        </div>
    </div>

    <!-- Scenario Narrative Prologue Modal -->
    <div class="modal-overlay" id="narrative-modal" style="display: none;" onclick="if(event.target === this) closeNarrativeModal()">
        <div class="modal-card narrative-card">
            <div class="narrative-top-bar">
                <div class="narrative-badge-kicker">
                    <span>📖</span> PROLOG SKENARIO
                </div>
                <button type="button" class="modal-close" onclick="closeNarrativeModal()">✕</button>
            </div>

            <div class="narrative-header">
                <div class="narrative-icon-wrap" id="narrative-icon-wrap">
                    <span id="narrative-icon">☕</span>
                </div>
                <div class="narrative-title-group">
                    <h3 id="narrative-title">Dikenalin Teman Tongkrongan</h3>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 4px;">
                        <span id="narrative-subtitle" class="narrative-subtitle">Kontak dari Rian</span>
                        <span id="narrative-badge" class="scenario-badge badge-initiator-aura">AURA Chat Duluan</span>
                    </div>
                </div>
            </div>

            <div class="narrative-scroll-area">
                <div class="narrative-story-box">
                    <div class="narrative-quote-decor">“</div>
                    <div id="narrative-body" class="narrative-body-text">
                        <!-- Populated dynamically by JavaScript -->
                    </div>
                </div>

                <div class="narrative-action-box" id="narrative-action-box">
                    <span class="narrative-action-icon" id="narrative-action-icon">💡</span>
                    <span class="narrative-action-text" id="narrative-action-text">
                        AURA sudah mengirimkan pesan pertama untuk menyapamu. Cek pesan masuknya!
                    </span>
                </div>
            </div>

            <div class="narrative-footer">
                <button type="button" class="narrative-start-btn" id="narrative-start-btn" onclick="closeNarrativeModal()">
                    <span id="narrative-start-btn-text">Masuk ke Obrolan ✨</span>
                </button>
            </div>
        </div>
    </div>
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

    // Mobile Sidebar Drawer Controller
    function toggleSidebar(open) {
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (!sidebar) return;
        if (open === undefined) {
            open = !sidebar.classList.contains('open');
        }
        if (open) {
            sidebar.classList.add('open');
            if (backdrop) backdrop.classList.add('open');
        } else {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('open');
        }
    }

    function getMoodPacing(mood) {
        switch (mood) {
            case 'happy':
                return {
                    readDelay: 2000,
                    unreadDelay: 0,
                    minTyping: 1200,
                    charSpeed: 18,
                    idleProactiveTime: 25000
                };
            case 'curious':
                return {
                    readDelay: 2200,
                    unreadDelay: 0,
                    minTyping: 1300,
                    charSpeed: 20,
                    idleProactiveTime: 25000
                };
            case 'shy':
                return {
                    readDelay: 2500,
                    unreadDelay: 0,
                    minTyping: 1400,
                    charSpeed: 22,
                    idleProactiveTime: 30000
                };
            case 'sulking':
                return {
                    readDelay: 10000,
                    unreadDelay: 6000,
                    minTyping: 1800,
                    charSpeed: 28,
                    idleProactiveTime: null
                };
            case 'concerned':
                return {
                    readDelay: 2500,
                    unreadDelay: 0,
                    minTyping: 1400,
                    charSpeed: 20,
                    idleProactiveTime: 28000
                };
            case 'sleepy':
                return {
                    readDelay: 6000,
                    unreadDelay: 3000,
                    minTyping: 1600,
                    charSpeed: 30,
                    idleProactiveTime: null
                };
            case 'melancholic':
                return {
                    readDelay: 5000,
                    unreadDelay: 2000,
                    minTyping: 1600,
                    charSpeed: 26,
                    idleProactiveTime: 35000
                };
            case 'peaceful':
            default:
                return {
                    readDelay: 3000,
                    unreadDelay: 0,
                    minTyping: 1400,
                    charSpeed: 22,
                    idleProactiveTime: 30000
                };
        }
    }

    function setHeaderStatus(text, isTyping = false) {
        const dot = document.getElementById('header-status-dot');
        const label = document.getElementById('header-status-text');
        if (!label) return;
        label.innerText = text;
        if (dot) {
            dot.style.display = 'inline-block';
            dot.style.backgroundColor = isTyping ? '#9D8CD7' : '#52B788';
        }
    }

    function updateMoodDisplay(moodObj) {
        if (!moodObj) return;
        currentAuraMood = moodObj.value || 'peaceful';
        const labelEl = document.getElementById('aura-mood-label');
        const emojiEl = document.getElementById('aura-mood-emoji');
        const pillEl = document.getElementById('aura-mood-pill');

        if (labelEl) labelEl.innerText = moodObj.label || 'Tenang & Santai';
        if (emojiEl) emojiEl.innerText = moodObj.emoji || '✨';

        if (pillEl) {
            pillEl.style.animation = 'none';
            void pillEl.offsetWidth;
            pillEl.style.animation = 'moodPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

            if (moodObj.value === 'sulking') {
                pillEl.style.background = '#FEE2E2';
                pillEl.style.borderColor = '#FCA5A5';
                pillEl.style.color = '#B91C1C';
                pillEl.title = 'AURA sedang ngambek: lama baca & slow response';
            } else if (moodObj.value === 'sleepy') {
                pillEl.style.background = '#E2E8F0';
                pillEl.style.borderColor = '#CBD5E1';
                pillEl.style.color = '#475569';
                pillEl.title = 'AURA sedang mengantuk: respon lambat & santai';
            } else if (moodObj.value === 'happy') {
                pillEl.style.background = '#FEF3C7';
                pillEl.style.borderColor = '#FDE68A';
                pillEl.style.color = '#92400E';
                pillEl.title = 'AURA sedang senang: fast response & antusias!';
            } else if (moodObj.value === 'curious') {
                pillEl.style.background = '#E0F2FE';
                pillEl.style.borderColor = '#BAE6FD';
                pillEl.style.color = '#0369A1';
                pillEl.title = 'AURA sedang kepo: penasaran & excited!';
            } else if (moodObj.value === 'shy') {
                pillEl.style.background = '#FCE7F3';
                pillEl.style.borderColor = '#FBCFE8';
                pillEl.style.color = '#9D174D';
                pillEl.title = 'AURA sedang tersipu / malu-malu kucing';
            } else {
                pillEl.style.background = 'var(--accent-lavender-light)';
                pillEl.style.borderColor = '#DDD6FE';
                pillEl.style.color = '#5B21B6';
                pillEl.title = 'Suasana hati AURA saat ini';
            }
        }
    }

    function updateStageDisplay(stageObj) {
        if (!stageObj) return;
        const labelEl = document.getElementById('aura-stage-label');
        const emojiEl = document.getElementById('aura-stage-emoji');
        const pillEl = document.getElementById('aura-stage-pill');

        if (labelEl) labelEl.innerText = stageObj.label || 'Baru Kenal';
        if (emojiEl) emojiEl.innerText = stageObj.emoji || '🌱';

        if (pillEl) {
            pillEl.style.animation = 'none';
            void pillEl.offsetWidth;
            pillEl.style.animation = 'moodPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

            if (stageObj.value === 'first_meeting') {
                pillEl.style.background = '#ECFDF5';
                pillEl.style.borderColor = '#A7F3D0';
                pillEl.style.color = '#065F46';
                pillEl.title = 'Tahap: Baru Kenal — AURA belum terlalu kenal kamu';
            } else if (stageObj.value === 'getting_to_know') {
                pillEl.style.background = '#E0F2FE';
                pillEl.style.borderColor = '#BAE6FD';
                pillEl.style.color = '#0369A1';
                pillEl.title = 'Tahap: Mulai Akrab — AURA sudah tahu nama & mulai nyaman';
            } else if (stageObj.value === 'close_friend') {
                pillEl.style.background = '#F3E8FF';
                pillEl.style.borderColor = '#D8B4FE';
                pillEl.style.color = '#6B21A8';
                pillEl.title = 'Tahap: Teman Dekat — Obrolan santai, akrab, & natural';
            }
        }
    }

    // Keep active scenario in state & update sidebar card
    function renderScenarioBanner(scenario) {
        if (!scenario) return;
        currentScenario = scenario;

        const sideTitle = document.getElementById('sidebar-scenario-title');
        const sidePremise = document.getElementById('sidebar-scenario-premise');

        if (sideTitle) {
            sideTitle.innerText = (scenario.icon ? scenario.icon + ' ' : '') + (scenario.title || 'Skenario Pertemuan');
        }
        if (sidePremise) {
            sidePremise.innerText = scenario.premise || '';
        }

        const input = document.getElementById('chat-input');
        if (input) {
            input.placeholder = 'Ketik pesan untuk AURA...';
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
            if (data.scenario) {
                renderScenarioBanner(data.scenario);
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
                    <div style="text-align: center; margin: 30px auto 15px auto; font-size: 11px; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px;">
                        HARI INI
                    </div>
                `;
            }

            const hasUserEverMessaged = Array.isArray(data.messages) && data.messages.some(m => m.sender === 'user');
            if (!hasShownInitialPrologue && data.scenario && !hasUserEverMessaged) {
                hasShownInitialPrologue = true;
                setTimeout(() => {
                    showNarrativeModal(data.scenario);
                }, 150);
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

    // Modal Scenario & Narrative Management
    let availableScenarios = [];
    let currentScenario = null;
    let hasShownInitialPrologue = false;

    function showNarrativeModal(scenario) {
        if (!scenario) return;
        currentScenario = scenario;

        const modal = document.getElementById('narrative-modal');
        if (!modal) return;

        const iconEl = document.getElementById('narrative-icon');
        const titleEl = document.getElementById('narrative-title');
        const subtitleEl = document.getElementById('narrative-subtitle');
        const badgeEl = document.getElementById('narrative-badge');
        const bodyEl = document.getElementById('narrative-body');
        const actionIconEl = document.getElementById('narrative-action-icon');
        const actionTextEl = document.getElementById('narrative-action-text');
        const btnTextEl = document.getElementById('narrative-start-btn-text');

        if (iconEl) iconEl.innerText = scenario.icon || '🌱';
        if (titleEl) titleEl.innerText = scenario.title || 'Skenario Pertemuan';
        if (subtitleEl) subtitleEl.innerText = scenario.subtitle || 'Kisah Awal';

        const isAura = scenario.initiator === 'aura';
        if (badgeEl) {
            badgeEl.innerText = isAura ? 'AURA Chat Duluan' : 'Kamu Chat Duluan';
            badgeEl.className = `scenario-badge ${isAura ? 'badge-initiator-aura' : 'badge-initiator-user'}`;
        }

        if (bodyEl) {
            const rawNarrative = scenario.narrative || scenario.premise || '';
            const paragraphs = rawNarrative.split(/\n\s*\n/).filter(p => p.trim().length > 0);
            if (paragraphs.length > 0) {
                bodyEl.innerHTML = paragraphs.map(p => `<p>${escapeHtml(p.trim())}</p>`).join('');
            } else {
                bodyEl.innerHTML = `<p>${escapeHtml(rawNarrative)}</p>`;
            }
        }

        if (actionIconEl) {
            actionIconEl.innerText = isAura ? '💬' : '✍️';
        }

        if (actionTextEl) {
            actionTextEl.innerText = scenario.narrative_action_text || (
                isAura
                    ? 'AURA sudah mengirimkan pesan pertama untuk menyapamu. Cek pesan masuknya!'
                    : 'Sekarang giliranmu untuk mengirimkan pesan pembuka kepada AURA!'
            );
        }

        if (btnTextEl) {
            btnTextEl.innerText = isAura ? 'Masuk ke Chat ✨' : 'Mulai Chat 💬';
        }

        modal.style.display = 'flex';
    }

    function closeNarrativeModal() {
        const modal = document.getElementById('narrative-modal');
        if (modal) modal.style.display = 'none';

        const input = document.getElementById('chat-input');
        if (input) {
            input.placeholder = 'Ketik pesan untuk AURA...';
            if (currentScenario && currentScenario.initiator === 'user') {
                input.focus();
            }
        }

        const scroll = document.getElementById('content-scroll');
        if (scroll) {
            setTimeout(() => {
                scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
            }, 50);
        }
    }

    function openCurrentNarrativeModal() {
        if (currentScenario) {
            showNarrativeModal(currentScenario);
        }
    }

    async function openScenarioModal() {
        const modal = document.getElementById('scenario-modal');
        if (!modal) return;

        modal.style.display = 'flex';

        if (availableScenarios.length === 0) {
            try {
                const res = await fetch('/api/v1/simulator/scenarios');
                const data = await res.json();
                if (data.success && Array.isArray(data.scenarios)) {
                    availableScenarios = data.scenarios;
                }
            } catch (e) {
                console.error('Failed to load scenarios:', e);
            }
        }

        const listEl = document.getElementById('scenario-list');
        if (!listEl) return;

        listEl.innerHTML = `
            <button type="button" class="scenario-item-btn random-btn" onclick="selectScenario(null)">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <b style="font-size: 13px; color: #5B21B6;">🎲 Pilih Acak (Surprise Me)</b>
                    <span class="scenario-badge" style="background: #DDD6FE; color: #4C1D95;">Random</span>
                </div>
                <div style="font-size: 11.5px; color: #6D28D9;">Sistem akan memilihkan skenario pertemuan unik secara acak!</div>
            </button>
        `;

        availableScenarios.forEach(s => {
            const isAura = s.initiator === 'aura';
            const badgeClass = isAura ? 'badge-initiator-aura' : 'badge-initiator-user';
            const badgeText = isAura ? 'AURA Chat Duluan' : 'Kamu Chat Duluan';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'scenario-item-btn';
            item.onclick = () => selectScenario(s.id);
            item.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <b style="font-size: 12.5px; color: var(--text-main);">${escapeHtml(s.icon)} ${escapeHtml(s.title)}</b>
                    <span class="scenario-badge ${badgeClass}">${badgeText}</span>
                </div>
                <div style="font-size: 11.5px; color: var(--text-sub); line-height: 1.4;">${escapeHtml(s.premise)}</div>
            `;
            listEl.appendChild(item);
        });
    }

    function closeScenarioModal() {
        const modal = document.getElementById('scenario-modal');
        if (modal) modal.style.display = 'none';
    }

    async function selectScenario(scenarioId) {
        closeScenarioModal();
        toggleSidebar(false);

        try {
            const res = await fetch('/api/v1/simulator/reset', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    scenario_id: scenarioId
                })
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
                hasShownInitialPrologue = true;
                setHeaderStatus('Virtual Life Online');

                await loadHistory();

                if (data.scenario) {
                    showNarrativeModal(data.scenario);
                }
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
                
                const clockText = charTime.getHours().toString().padStart(2, '0') + ':' + charTime.getMinutes().toString().padStart(2, '0') + ' WIB';
                const clockEl = document.getElementById('local-clock');
                if (clockEl) clockEl.innerText = clockText;

                if (cur) {
                    const titleEl = document.getElementById('act-title');
                    const locEl = document.getElementById('act-loc');
                    const descEl = document.getElementById('act-desc');
                    const badgeEl = document.getElementById('badge-status');
                    const nextEl = document.getElementById('act-next');
                    const headerActEl = document.getElementById('chat-header-activity');

                    const locName = (cur.location ? cur.location.name : 'Kamar AURA');

                    if (titleEl) titleEl.innerText = cur.name;
                    if (locEl) locEl.innerText = locName;
                    if (descEl) descEl.innerText = cur.description || 'Sedang menikmati rutinitas harian dengan tenang.';
                    if (badgeEl) badgeEl.innerText = (cur.mood_presentation ? 'Mood: ' + cur.mood_presentation : 'Aktif');
                    
                    if (nextEl && json.data.next_planned_activity) {
                        nextEl.innerText = 'Selanjutnya: ' + json.data.next_planned_activity.name;
                    }

                    if (headerActEl && !isAuraProcessing && !isAuraReading) {
                        headerActEl.innerHTML = `
                            <span id="header-status-dot" class="status-dot"></span>
                            <span id="header-status-text">📍 ${escapeHtml(locName)} • ${escapeHtml(cur.name)}</span>
                        `;
                    }
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function appendUserBubbleImmediate(text, isFirstInBatch = false) {
        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');
        const time = getTimeString();
        const tickId = 'tick-' + Date.now() + '-' + Math.random().toString(36).substr(2, 4);

        let quotedHtml = '';
        if (isFirstInBatch && activeReplyForBatch && activeReplyForBatch.content) {
            quotedHtml = `
                <div class="quoted-box">
                    <div class="quoted-author">${escapeHtml(activeReplyForBatch.sender || 'AURA')}</div>
                    <div class="quoted-text">${escapeHtml(activeReplyForBatch.content)}</div>
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
                <div class="bubble-text-content">${escapeHtml(text)}</div>
                <div class="bubble-meta">
                    <span>${time}</span>
                    <span class="tick-container" id="${tickId}">${getSingleTickSvg()}</span>
                </div>
            </div>
        `;
        chatBox.appendChild(wrap);
        conversationHistory.push({ role: 'user', content: text });
        unreadTickIds.push(tickId);

        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
    }

    function transitionTicksToDoubleGrey() {
        unreadTickIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerHTML = getDoubleTickSvg(false);
            }
        });
    }

    function transitionTicksToBlue() {
        unreadTickIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerHTML = getDoubleTickSvg(true);
            }
        });
        unreadTickIds.length = 0;
    }

    function resetIdleProactiveTimer() {
        stopProactiveTimer();

        if (hasProactedThisSession) return;

        const pacing = getMoodPacing(currentAuraMood);
        if (!pacing.idleProactiveTime) return;

        idleProactiveTimer = setTimeout(async () => {
            if (isAuraProcessing || isAuraReading || userIsTyping || hasProactedThisSession) return;
            await triggerProactiveMessage();
        }, pacing.idleProactiveTime);
    }

    function stopProactiveTimer() {
        if (idleProactiveTimer) {
            clearTimeout(idleProactiveTimer);
            idleProactiveTimer = null;
        }
    }

    async function triggerProactiveMessage() {
        hasProactedThisSession = true;
        isAuraProcessing = true;
        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        setHeaderStatus('AURA sedang mengetik...', true);
        const typingIndicator = createTypingIndicator();
        chatBox.appendChild(typingIndicator);
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        try {
            const res = await fetch('/api/v1/simulator/proactive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });
            const data = await res.json();
            if (typingIndicator.parentNode) typingIndicator.parentNode.removeChild(typingIndicator);

            if (data.success && data.replies && data.replies.length > 0) {
                if (data.current_mood) {
                    updateMoodDisplay(data.current_mood);
                }
                const pacing = getMoodPacing(currentAuraMood);

                for (let i = 0; i < data.replies.length; i++) {
                    const bubbleText = data.replies[i];

                    if (i > 0) {
                        setHeaderStatus('AURA sedang mengetik...', true);
                        chatBox.appendChild(typingIndicator);
                        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });
                        const typingDuration = Math.min(2200, Math.max(900, bubbleText.length * pacing.charSpeed));
                        await sleep(typingDuration);
                        if (typingIndicator.parentNode) typingIndicator.parentNode.removeChild(typingIndicator);
                    }

                    const time = getTimeString();
                    const wrap = document.createElement('div');
                    wrap.className = 'bubble-wrap bubble-wrap-aura';
                    wrap.innerHTML = `
                        <div class="bubble bubble-aura" ondblclick="startReplyFromBubble(this)">
                            <div class="bubble-text-content">${escapeHtml(bubbleText)}</div>
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
                    conversationHistory.push({ role: 'model', content: bubbleText });
                    scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

                    if (i < data.replies.length - 1) {
                        await sleep(400);
                    }
                }
            }
        } catch (e) {
            console.error('Proactive message error:', e);
            if (typingIndicator.parentNode) typingIndicator.parentNode.removeChild(typingIndicator);
        } finally {
            isAuraProcessing = false;
            setHeaderStatus('Virtual Life Online');
        }
    }

    function startReply(btn) {
        const wrap = btn.closest('.bubble-wrap');
        if (!wrap) return;
        const bubble = wrap.querySelector('.bubble');
        if (!bubble) return;
        startReplyFromBubble(bubble);
    }

    function startReplyFromBubble(bubbleEl) {
        const isUser = bubbleEl.classList.contains('bubble-user');
        const textContentEl = bubbleEl.querySelector('.bubble-text-content');
        const text = textContentEl ? textContentEl.innerText : bubbleEl.innerText;

        currentReply = {
            sender: isUser ? 'Kamu' : 'AURA',
            content: text.trim()
        };

        const bar = document.getElementById('reply-preview-bar');
        const authorEl = document.getElementById('reply-bar-author');
        const snippetEl = document.getElementById('reply-bar-snippet');
        const input = document.getElementById('chat-input');

        if (authorEl) authorEl.innerText = 'Membalas ' + currentReply.sender;
        if (snippetEl) snippetEl.innerText = currentReply.content;
        if (bar) bar.style.display = 'flex';

        if (input) input.focus();
    }

    function cancelReply() {
        currentReply = null;
        const bar = document.getElementById('reply-preview-bar');
        if (bar) bar.style.display = 'none';
    }

    function handleSend(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        input.focus();

        stopProactiveTimer();

        const isFirstInBatch = pendingUserMessages.length === 0;
        if (isFirstInBatch) {
            activeReplyForBatch = currentReply ? { ...currentReply } : null;
            cancelReply();
        }

        pendingUserMessages.push(text);
        appendUserBubbleImmediate(text, isFirstInBatch);

        clearTimeout(silenceTimer);

        if (!isAuraReading && !isAuraProcessing) {
            silenceTimer = setTimeout(() => {
                triggerAuraResponseBatch();
            }, 3000);
        }
    }

    async function triggerAuraResponseBatch() {
        if (pendingUserMessages.length === 0) return;
        if (isAuraProcessing) return;

        isAuraProcessing = true;
        isAuraReading = true;

        const messagesToSend = [...pendingUserMessages];
        pendingUserMessages.length = 0;
        const replyContextToSend = activeReplyForBatch ? { ...activeReplyForBatch } : null;
        activeReplyForBatch = null;

        const pacing = getMoodPacing(currentAuraMood);

        if (pacing.unreadDelay > 0) {
            await sleep(pacing.unreadDelay);
        }

        transitionTicksToDoubleGrey();

        await sleep(pacing.readDelay);

        transitionTicksToBlue();
        isAuraReading = false;

        const chatBox = document.getElementById('chat-box');
        const scroll = document.getElementById('content-scroll');

        setHeaderStatus('AURA sedang mengetik...', true);
        const typingIndicator = createTypingIndicator();
        chatBox.appendChild(typingIndicator);
        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

        try {
            const res = await fetch('/api/v1/simulator/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    messages: messagesToSend,
                    reply_to: replyContextToSend
                })
            });
            const data = await res.json();

            if (typingIndicator.parentNode) {
                typingIndicator.parentNode.removeChild(typingIndicator);
            }

            if (data.success) {
                if (data.relationship_stage) {
                    updateStageDisplay(data.relationship_stage);
                }
                if (data.current_mood) {
                    updateMoodDisplay(data.current_mood);
                }
                if (data.scenario) {
                    renderScenarioBanner(data.scenario);
                }

                const updatedPacing = getMoodPacing(currentAuraMood);

                for (let i = 0; i < data.replies.length; i++) {
                    const bubbleText = data.replies[i];

                    if (i > 0) {
                        setHeaderStatus('AURA sedang mengetik...', true);
                        chatBox.appendChild(typingIndicator);
                        scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

                        const typingDuration = Math.min(2400, Math.max(900, bubbleText.length * updatedPacing.charSpeed));
                        await sleep(typingDuration);

                        if (typingIndicator.parentNode) {
                            typingIndicator.parentNode.removeChild(typingIndicator);
                        }
                    } else {
                        const firstTypingDuration = Math.min(1800, Math.max(updatedPacing.minTyping, bubbleText.length * 15));
                        await sleep(firstTypingDuration);
                    }

                    const time = getTimeString();
                    const wrap = document.createElement('div');
                    wrap.className = 'bubble-wrap bubble-wrap-aura';
                    wrap.innerHTML = `
                        <div class="bubble bubble-aura" ondblclick="startReplyFromBubble(this)">
                            <div class="bubble-text-content">${escapeHtml(bubbleText)}</div>
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
                    conversationHistory.push({ role: 'model', content: bubbleText });
                    scroll.scrollTo({ top: scroll.scrollHeight, behavior: 'smooth' });

                    if (i < data.replies.length - 1) {
                        await sleep(350);
                    }
                }
            } else {
                alert(data.reply || 'Terjadi kesalahan');
            }
        } catch (e) {
            console.error(e);
            if (typingIndicator.parentNode) {
                typingIndicator.parentNode.removeChild(typingIndicator);
            }
        } finally {
            isAuraProcessing = false;
            setHeaderStatus('Virtual Life Online');
            resetIdleProactiveTimer();

            if (pendingUserMessages.length > 0) {
                silenceTimer = setTimeout(() => {
                    triggerAuraResponseBatch();
                }, 1500);
            }
        }
    }

    const chatInputField = document.getElementById('chat-input');
    if (chatInputField) {
        chatInputField.addEventListener('input', () => {
            userIsTyping = true;
            stopProactiveTimer();
            clearTimeout(userTypingTimer);
            userTypingTimer = setTimeout(() => {
                userIsTyping = false;
                resetIdleProactiveTimer();
            }, 3000);
        });
    }

    // Initialize application
    window.addEventListener('DOMContentLoaded', async () => {
        await loadStatus();
        await loadHistory();
        setInterval(loadStatus, 25000);
        resetIdleProactiveTimer();
    });
</script>
</body>
</html>