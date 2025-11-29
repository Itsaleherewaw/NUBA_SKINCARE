class ChatBot {
    constructor() {
        this.isOpen = false;
        this.init();
    }

    init() {
        this.createChatInterface();
        this.loadConversation();
    }

    createChatInterface() {
        const chatHTML = `
            <div id="chatbot-container" class="chatbot-container">
                <div id="chatbot-header" class="chatbot-header">
                    <span>Asistente NUBA</span>
                    <button id="chatbot-close" class="chatbot-close">×</button>
                </div>
                <div id="chatbot-messages" class="chatbot-messages"></div>
                <div class="chatbot-input">
                    <input type="text" id="chatbot-input" placeholder="Escribe tu mensaje...">
                    <button id="chatbot-send">Enviar</button>
                </div>
            </div>
            <button id="chatbot-toggle" class="chatbot-toggle">
                💬
            </button>
        `;

        document.body.insertAdjacentHTML('beforeend', chatHTML);
        this.bindEvents();
    }

    bindEvents() {
        document.getElementById('chatbot-toggle').addEventListener('click', () => this.toggleChat());
        document.getElementById('chatbot-close').addEventListener('click', () => this.toggleChat());
        document.getElementById('chatbot-send').addEventListener('click', () => this.sendMessage());
        document.getElementById('chatbot-input').addEventListener('keypress', (e) => {
            if(e.key === 'Enter') this.sendMessage();
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const container = document.getElementById('chatbot-container');
        const toggle = document.getElementById('chatbot-toggle');
        
        if(this.isOpen) {
            container.style.display = 'flex';
            toggle.style.display = 'none';
            document.getElementById('chatbot-input').focus();
        } else {
            container.style.display = 'none';
            toggle.style.display = 'flex';
        }
    }

    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if(!message) return;

        this.addMessage(message, false);
        input.value = '';

        // Simular typing
        this.showTypingIndicator();

        try {
            const response = await this.getBotResponse(message);
            setTimeout(() => {
                this.hideTypingIndicator();
                this.addMessage(response, true);
            }, 1000);
        } catch(error) {
            this.hideTypingIndicator();
            this.addMessage('Lo siento, hubo un error. Por favor intenta nuevamente.', true);
        }
    }

    async getBotResponse(message) {
        try {
            const response = await fetch('/NUBA_SKINCARE/public/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            return data.response;
        } catch(error) {
            return this.getFallbackResponse(message);
        }
    }

    getFallbackResponse(message) {
        const messageLower = message.toLowerCase();
        
        const responses = {
            'hola': '¡Hola! Soy el asistente virtual de NUBA. ¿En qué puedo ayudarte hoy?',
            'productos': 'Tenemos sérums, limpiadores, cremas, protectores solares y aceites faciales. ¿Te interesa algún producto en particular?',
            'precios': 'Todos nuestros precios están en Bolivianos (Bs.). Puedes ver los precios específicos en nuestra sección de productos.',
            'envío': 'Realizamos envíos a todo Bolivia. El costo y tiempo de entrega dependen de tu ubicación.',
            'contacto': 'Puedes contactarnos al +591 70514802 o escribirnos a info@nuba.com',
            'horario': 'Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00 y sábados de 9:00 a 14:00.',
            'gracias': '¡De nada! Estoy aquí para ayudarte. ¿Necesitas algo más?'
        };

        for(const [key, response] of Object.entries(responses)) {
            if(messageLower.includes(key)) {
                return response;
            }
        }

        return 'Entiendo que quieres saber sobre: "' + message + '". Te recomiendo visitar nuestra sección de productos o contactar con nuestro equipo de atención al cliente para información más específica.';
    }

    addMessage(text, isBot) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${isBot ? 'bot-message' : 'user-message'}`;
        messageDiv.textContent = text;
        
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    showTypingIndicator() {
        const messagesContainer = document.getElementById('chatbot-messages');
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'chat-message bot-message typing';
        typingDiv.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
        
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if(typingIndicator) {
            typingIndicator.remove();
        }
    }

    loadConversation() {
        // Cargar historial de conversación si existe
        const savedMessages = localStorage.getItem('nuba_chat_history');
        if(savedMessages) {
            const messages = JSON.parse(savedMessages);
            messages.forEach(msg => {
                this.addMessage(msg.text, msg.isBot);
            });
        }
    }
}

// Inicializar el chat cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new ChatBot();
});