<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Absensi PKL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: #f97316;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .logo-img {
            height: 80px;
            width: auto;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .navbar-nav {
            display: flex;
            gap: 5px;
            margin: 0 auto;
        }

        .navbar-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 14px;
        }

        .navbar-nav a[href*="admin"] {
            background: rgba(255, 255, 255, 0.15);
            margin-left: 10px;
        }

        .navbar-nav a[href*="admin"]:hover,
        .navbar-nav a[href*="admin"].active {
            background: rgba(255, 255, 255, 0.3);
        }

        .navbar-nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .navbar-nav a.active {
            background: rgba(255, 255, 255, 0.3);
        }

        .navbar-end {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            color: white;
            font-size: 14px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 16px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #f97316;
            margin-bottom: 10px;
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            text-align: center;
        }

        .btn:hover {
            background: #ea580c;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        /* Toast/Popup Styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease-out, slideOut 0.3s ease-out 2.7s forwards;
            z-index: 1000;
            max-width: 300px;
            word-wrap: break-word;
        }

        .toast.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* Floating Chatbot Button */
        .floating-chatbot-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.4);
            z-index: 999;
            transition: all 0.3s ease;
            color: white;
            font-size: 28px;
        }

        .floating-chatbot-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(249, 115, 22, 0.6);
        }

        .floating-chatbot-btn:active {
            transform: scale(0.95);
        }

        /* Chatbot Modal Styles */
        .chatbot-modal {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 450px;
            max-height: 600px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 998;
            flex-direction: column;
            animation: slideUp 0.3s ease-out;
        }

        .chatbot-modal.active {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-modal-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 16px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px 12px 0 0;
        }

        .chatbot-modal-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chatbot-modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
        }

        .chatbot-modal-close:hover {
            opacity: 0.8;
        }

        .chatbot-modal-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chatbot-modal-message {
            display: flex;
            margin-bottom: 8px;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-modal-message.user {
            justify-content: flex-end;
        }

        .chatbot-modal-message.bot {
            justify-content: flex-start;
        }

        .chatbot-modal-message-content {
            max-width: 70%;
            padding: 10px 12px;
            border-radius: 8px;
            word-wrap: break-word;
            white-space: pre-wrap;
            line-height: 1.5;
            font-size: 14px;
        }

        .chatbot-modal-message.user .chatbot-modal-message-content {
            background: #f97316;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .chatbot-modal-message.bot .chatbot-modal-message-content {
            background: white;
            color: #1a1a1a;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 2px;
        }

        .chatbot-modal-footer {
            padding: 12px;
            border-top: 1px solid #e9ecef;
            background: white;
            border-radius: 0 0 12px 12px;
        }

        .chatbot-modal-input-group {
            display: flex;
            gap: 8px;
        }

        .chatbot-modal-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .chatbot-modal-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .chatbot-modal-send-btn {
            padding: 10px 16px;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .chatbot-modal-send-btn:hover {
            background: #ea580c;
            transform: translateY(-1px);
        }

        .chatbot-modal-send-btn:active {
            transform: translateY(0);
        }

        .chatbot-modal-info {
            background: #cfe2ff;
            border: 1px solid #b6d4fe;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            color: #084298;
            font-size: 13px;
        }

        .chatbot-modal-loading {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #6c757d;
            opacity: 0.6;
            animation: pulse 1s infinite;
            margin-right: 4px;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.6;
            }
            50% {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .chatbot-modal {
                width: calc(100% - 20px);
                max-height: 70vh;
                bottom: 80px;
                right: 10px;
                left: 10px;
            }

            .chatbot-modal-message-content {
                max-width: 85%;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
            }

            .navbar-nav {
                flex-wrap: wrap;
                width: 100%;
                justify-content: center;
            }

            .container {
                padding: 20px 15px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .floating-chatbot-btn {
                bottom: 20px;
                right: 20px;
                width: 55px;
                height: 55px;
                font-size: 24px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Toast Messages -->
    @if (session('success'))
        <div class="toast success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="toast error">
            <i class="bi bi-x-circle-fill" style="margin-right: 8px;"></i>{{ session('error') }}
        </div>
    @endif

    <nav class="navbar">
        <a href="/dashboard" class="navbar-brand">
            <img src="https://www.permataharapanku.sch.id/images/logo_sph.png" alt="School Logo">
            Absensi PKL
        </a>

        <div class="navbar-nav">
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('notifications.index') }}" class="{{ request()->is('notifications*') ? 'active' : '' }}" style="position: relative;">
                <i class="bi bi-bell" style="margin-right: 4px;"></i>Notifications
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span style="position: absolute; top: -5px; right: -10px; background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 11px; font-weight: 700;">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
            @if(!auth()->user()->hasRole('admin'))
                <a href="/absence" class="{{ request()->is('absence*') && !request()->is('absence/pending*') ? 'active' : '' }}">Absence</a>
            @endif
            @if(auth()->user()->hasAnyRole(['homeroom_teacher', 'head_of_department', 'industry_supervisor', 'school_principal', 'admin']))
                <a href="{{ route('absence.pending') }}" class="{{ request()->is('absence/pending*') ? 'active' : '' }}" style="position: relative;">
                    <i class="bi bi-check-square" style="margin-right: 4px;"></i>Approve
                </a>
                <a href="{{ route('absence.all') }}" class="{{ request()->is('absence/all*') ? 'active' : '' }}">
                    <i class="bi bi-list-check" style="margin-right: 4px;"></i>All Absences
                </a>
                <a href="{{ route('qrcode.index') }}" class="{{ request()->is('qrcode*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code" style="margin-right: 4px;"></i>QR Codes
                </a>
            @endif
            @if(!auth()->user()->hasRole('student'))
                <a href="{{ route('reports.index') }}" class="{{ request()->is('reports*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart" style="margin-right: 4px;"></i>Reports
                </a>
            @endif
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}" style="margin-left: 20px;">
                    <i class="bi bi-people" style="margin-right: 5px;"></i>Users
                </a>
                <a href="{{ route('admin.roles') }}" class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock" style="margin-right: 5px;"></i>Roles
                </a>
            @endif
        </div>

        <div class="navbar-end">
            <a href="{{ route('settings.index') }}" class="{{ request()->is('settings*') ? 'active' : '' }}" style="color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; transition: background 0.3s;">
                <i class="bi bi-gear" style="margin-right: 4px;"></i>Settings
            </a>
            <div class="user-info">
                Welcome, <strong>{{ Auth::user()->name }}</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <!-- Floating Chatbot Button -->
    <button class="floating-chatbot-btn" id="chatbotBtn" title="Chat with us">
        <i class="bi bi-chat-dots-fill"></i>
    </button>

    <!-- Chatbot Modal -->
    <div class="chatbot-modal" id="chatbotModal">
        <div class="chatbot-modal-header">
            <h2>
                <i class="bi bi-chat-dots"></i> Assistance Bot
            </h2>
            <button class="chatbot-modal-close" id="chatbotClose">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <div class="chatbot-modal-messages" id="chatbotMessagesContainer">
            <div class="chatbot-modal-info">
                <strong>💡 Tip:</strong> Tanyakan tentang absensi, program PKL, atau ketik "bantuan" untuk melihat perintah yang tersedia.
            </div>

            <div class="chatbot-modal-message bot">
                <div class="chatbot-modal-message-content">
                    Halo! 👋 Saya adalah chatbot Absensi PKL. Saya siap membantu Anda mengetahui status absensi, program PKL, dan informasi lainnya. Ada yang bisa saya bantu?
                </div>
            </div>
        </div>

        <div class="chatbot-modal-footer">
            <div class="chatbot-modal-input-group">
                <input 
                    type="text" 
                    id="chatbotModalInput" 
                    class="chatbot-modal-input" 
                    placeholder="Ketik pesan Anda..." 
                    autocomplete="off"
                />
                <button class="chatbot-modal-send-btn" onclick="sendChatbotMessage()">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Chatbot Modal Control
        const chatbotBtn = document.getElementById('chatbotBtn');
        const chatbotModal = document.getElementById('chatbotModal');
        const chatbotClose = document.getElementById('chatbotClose');
        const messageInput = document.getElementById('chatbotModalInput');
        const messagesContainer = document.getElementById('chatbotMessagesContainer');

        // Open modal
        chatbotBtn.addEventListener('click', () => {
            chatbotModal.classList.add('active');
            messageInput.focus();
        });

        // Close modal
        chatbotClose.addEventListener('click', () => {
            chatbotModal.classList.remove('active');
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && chatbotModal.classList.contains('active')) {
                chatbotModal.classList.remove('active');
            }
        });

        // Send message with Enter
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChatbotMessage();
            }
        });

        function sendChatbotMessage() {
            const message = messageInput.value.trim();
            
            if (!message) return;

            // Show user message
            addChatbotMessage(message, 'user');

            // Clear input
            messageInput.value = '';
            messageInput.focus();

            // Show loading indicator
            showChatbotLoadingIndicator();

            // Send to backend
            fetch('{{ route("chatbot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                removeChatbotLoadingIndicator();
                if (data.success) {
                    addChatbotMessage(data.reply, 'bot');
                } else {
                    addChatbotMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                }
            })
            .catch(error => {
                removeChatbotLoadingIndicator();
                console.error('Error:', error);
                addChatbotMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.', 'bot');
            });
        }

        function addChatbotMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-modal-message ${sender}`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'chatbot-modal-message-content';
            contentDiv.textContent = text;
            
            messageDiv.appendChild(contentDiv);
            messagesContainer.appendChild(messageDiv);

            // Auto scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function showChatbotLoadingIndicator() {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chatbot-modal-message bot';
            messageDiv.id = 'chatbotLoadingMessage';
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'chatbot-modal-message-content';
            contentDiv.innerHTML = '<span class="chatbot-modal-loading"></span><span class="chatbot-modal-loading"></span><span class="chatbot-modal-loading"></span>';
            
            messageDiv.appendChild(contentDiv);
            messagesContainer.appendChild(messageDiv);

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function removeChatbotLoadingIndicator() {
            const loadingMessage = document.getElementById('chatbotLoadingMessage');
            if (loadingMessage) {
                loadingMessage.remove();
            }
        }
    </script>

    @yield('scripts')
</body>
</html>
