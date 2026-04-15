@extends('layouts.app')

@section('content')
<style>
    * {
        box-sizing: border-box;
    }

    .chatbot-container {
        max-width: 800px;
        margin: 2rem auto;
        height: 600px;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .chatbot-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        display: flex;
        margin-bottom: 1rem;
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

    .message.user {
        justify-content: flex-end;
    }

    .message.bot {
        justify-content: flex-start;
    }

    .message-content {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        word-wrap: break-word;
        white-space: pre-wrap;
        line-height: 1.5;
    }

    .message.user .message-content {
        background: #f97316;
        color: white;
        border-bottom-right-radius: 0.25rem;
    }

    .message.bot .message-content {
        background: white;
        color: #1a1a1a;
        border: 1px solid #e9ecef;
        border-bottom-left-radius: 0.25rem;
    }

    .chatbot-footer {
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
        background: white;
    }

    .input-group {
        display: flex;
        gap: 0.75rem;
    }

    #messageInput {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        font-size: 0.95rem;
        font-family: inherit;
    }

    #messageInput:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    .send-btn {
        padding: 0.75rem 1.5rem;
        background: #f97316;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .send-btn:hover {
        background: #ea580c;
        transform: translateY(-1px);
    }

    .send-btn:active {
        transform: translateY(0);
    }

    .loading {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6c757d;
        opacity: 0.6;
        animation: pulse 1s infinite;
        margin-right: 0.5rem;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 0.6;
        }
        50% {
            opacity: 1;
        }
    }

    .chatbot-info {
        background: #cfe2ff;
        border: 1px solid #b6d4fe;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        color: #084298;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .chatbot-container {
            height: 500px;
            margin: 1rem;
        }

        .message-content {
            max-width: 90%;
        }

        .chatbot-header h2 {
            font-size: 1.25rem;
        }
    }
</style>

<div class="chatbot-container">
    <!-- Header -->
    <div class="chatbot-header">
        <h2>
            <i class="bi bi-chat-dots"></i> Assistance Bot
        </h2>
    </div>

    <!-- Messages Area -->
    <div class="chatbot-messages" id="messagesContainer">
        <div class="chatbot-info">
            <strong>💡 Tip:</strong> Tanyakan tentang absensi, program PKL, atau ketik "bantuan" untuk melihat perintah yang tersedia.
        </div>

        <!-- Welcome Message -->
        <div class="message bot">
            <div class="message-content">
                Halo! 👋 Saya adalah chatbot Absensi PKL. Saya siap membantu Anda mengetahui status absensi, program PKL, dan informasi lainnya. Ada yang bisa saya bantu?
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="chatbot-footer">
        <div class="input-group">
            <input 
                type="text" 
                id="messageInput" 
                placeholder="Ketik pesan Anda..." 
                autocomplete="off"
            />
            <button class="send-btn" onclick="sendMessage()">
                <i class="bi bi-send"></i> Kirim
            </button>
        </div>
    </div>
</div>

<script>
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.querySelector('.send-btn');

    // Kirim pesan dengan Enter
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        
        if (!message) return;

        // Tampilkan pesan user
        addMessage(message, 'user');

        // Kosongkan input
        messageInput.value = '';
        messageInput.focus();

        // Tampilkan loading indicator
        showLoadingIndicator();

        // Kirim ke backend
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
            removeLoadingIndicator();
            if (data.success) {
                addMessage(data.reply, 'bot');
            } else {
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
            }
        })
        .catch(error => {
            removeLoadingIndicator();
            console.error('Error:', error);
            addMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.', 'bot');
        });
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = text;
        
        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);

        // Auto scroll ke bawah
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function showLoadingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot';
        messageDiv.id = 'loadingMessage';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = '<span class="loading"></span><span class="loading"></span><span class="loading"></span>';
        
        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);

        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function removeLoadingIndicator() {
        const loadingMessage = document.getElementById('loadingMessage');
        if (loadingMessage) {
            loadingMessage.remove();
        }
    }

    // Auto focus ke input saat halaman dimuat
    window.addEventListener('load', () => {
        messageInput.focus();
    });
</script>
@endsection
