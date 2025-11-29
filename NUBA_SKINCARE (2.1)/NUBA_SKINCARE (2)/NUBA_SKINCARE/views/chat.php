<?php
/**
 * Vista principal del chat. Se debe incluir desde tu layout.
 */
?>

<link rel="stylesheet" href="/public/assets/css/chat.css">
<div id="chat-float-btn"><img src="/public/assets/img/chat_icon.png" alt="Chat"></div>

<div id="chat-widget" class="chat-widget">
    <div class="chat-header">
        <span>Chat Soporte</span>
        <button id="chat-close">&times;</button>
    </div>
    <div id="chat-history" class="chat-history"></div>
    <form id="chat-form">
        <input type="text" id="chat-input" maxlength="500" placeholder="Escribe aquí..." autocomplete="off" />
        <button type="submit">Enviar</button>
    </form>
</div>
<script src="/public/assets/js/chat.js"></script>