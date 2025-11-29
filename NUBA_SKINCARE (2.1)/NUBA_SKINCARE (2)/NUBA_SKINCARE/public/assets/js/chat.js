// Chat script modular. Maneja UI flotante, AJAX y refresco suave.

document.addEventListener('DOMContentLoaded', function() {
    const chatBtn = document.getElementById('chat-float-btn');
    const chatWidget = document.getElementById('chat-widget');
    const chatHistory = document.getElementById('chat-history');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatClose = document.getElementById('chat-close');

    // Mostrar/ocultar chat
    chatBtn.onclick = () => chatWidget.classList.toggle('active');
    chatClose.onclick = () => chatWidget.classList.remove('active');

    // Cargar historial (AJAX)
    function loadHistory() {
        fetch('/chat/fetchHistory')
            .then(res => res.json())
            .then(data => {
                chatHistory.innerHTML = '';
                data.forEach(msg => {
                    const li = document.createElement('div');
                    li.className = `message ${msg.sender}`;
                    li.innerHTML = `<span>${msg.sender === 'user' ? 'Yo' : 'Bot'}:</span> ${msg.message}`;
                    chatHistory.appendChild(li);
                });
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
    }
    loadHistory();

    // Envío de mensajes
    chatForm.onsubmit = function(e) {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg || msg.length > 500) return;

        fetch('/chat/sendMessage', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: msg })
        })
        .then(res => res.json())
        .then(data => {
            chatInput.value = '';
            loadHistory();
        });
    };

    // Refresco periódico suave
    setInterval(loadHistory, 5000);
});