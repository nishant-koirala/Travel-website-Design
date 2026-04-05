<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Widget</title>
    <style>
        /* Floating chat button */
        #chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Chat window */
        #chat-window {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            max-height: 500px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 1000;
        }

        /* Header of the chat window */
        #chat-header {
            background: #007bff;
            color: #fff;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chat-header .close {
            cursor: pointer;
        }

        /* Messages */
        #chat-messages {
            padding: 10px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Message container */
        .message {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .message.admin {
            flex-direction: row-reverse;
        }

        .message .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .message.admin .avatar {
            margin-left: 10px;
            margin-right: 0;
        }

        .message .text {
            background: #007bff;
            padding: 10px;
            border-radius: 10px;
            max-width: 200px;
        }

        .message.admin .text {
            background: #f1f1f1;
            color: black;
        }

        /* Form area */
        #chat-form {
            padding: 10px;
            display: flex;
            flex-direction: column;
            border-top: 1px solid #ddd;
        }

        #chat-form textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none;
        }

        #chat-form button {
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: #fff;
            cursor: pointer;
        }

        #chat-form button:disabled {
            background: blue;
            cursor: not-allowed;
        }
        #message-form button[type="submit"] {
    background-color: #007bff; /* Primary color */
    color: #ffffff; /* Text color */
    border: none;
    border-radius: 5px; /* Rounded corners */
    padding: 5px 10px; /* Padding */
    font-size: 12px; /* Font size */
    font-weight: bold; /* Font weight */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
}

        #message-form button[type="submit"]:hover {
    background-color: #0056b3; /* Darker shade for hover */
    transform: translateY(-2px); /* Slight lift effect */
}
        .chatbot-quick { padding: 6px 10px 0; display: flex; flex-wrap: wrap; gap: 6px; }
        .chatbot-quick button {
            font-size: 11px; padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px;
            background: #f8f9fa; cursor: pointer; color: #007bff;
        }
        .chatbot-quick button:hover { background: #e9ecef; }
    </style>
</head>
<body>
    <!-- Floating chat button -->
    <div id="chat-widget">
        <span>Chat here</span>
    </div>

    <!-- Chat window -->
    <div id="chat-window">
        <div id="chat-header">
            <span>Chat with us</span>
            <span class="close">&times;</span>
        </div>
        <div id="chat-messages">
            <!-- Messages will be inserted here -->
        </div>
        <div id="chat-form">
            <div class="chatbot-quick">
                <button type="button" data-q="What is the cheapest package?">Cheapest trip</button>
                <button type="button" data-q="Show me the most expensive luxury package">Luxury trip</button>
                <button type="button" data-q="I want to speak to a human">Human agent</button>
            </div>
            <textarea id="message-input" rows="3" placeholder="Type your message..."></textarea>
            <button id="send-message">Send</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatWidget = document.getElementById('chat-widget');
            const chatWindow = document.getElementById('chat-window');
            const closeButton = document.querySelector('#chat-header .close');
            const messageInput = document.getElementById('message-input');
            const sendMessageButton = document.getElementById('send-message');
            const chatMessages = document.getElementById('chat-messages');
            const CHATBOT_URL = (typeof window.CHATBOT_API === 'string' && window.CHATBOT_API)
                ? window.CHATBOT_API
                : 'chatbot/chat.php';

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function showLegacyContactForm() {
                chatMessages.innerHTML += `
                    <div class="message admin">
                        <img src="images/img-1.jpg" class="avatar" alt="Admin Avatar">
                        <div class="text" id="response-form">
                            <form id="message-form">
                                <label>Name: <input type="text" name="name" required></label><br>
                                <label>Email: <input type="email" name="email" required></label><br>
                                <label>Phone Number: <input type="text" name="phone" required></label><br>
                                <label>Message: <textarea name="message" required></textarea></label><br>
                                <button type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                `;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Toggle chat window
            chatWidget.addEventListener('click', function() {
                chatWindow.style.display = 'flex';
                chatWidget.style.display = 'none';
            });

            closeButton.addEventListener('click', function() {
                chatWindow.style.display = 'none';
                chatWidget.style.display = 'flex';
            });

            document.querySelectorAll('.chatbot-quick [data-q]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const q = this.getAttribute('data-q');
                    if (q) {
                        postChatMessage(q);
                    }
                });
            });

            function postChatMessage(message) {
                if (!message || !message.trim()) {
                    return;
                }
                message = message.trim();
                chatMessages.innerHTML += `
                    <div class="message user">
                        <div class="text">${escapeHtml(message)}</div>
                    </div>
                `;
                messageInput.value = '';

                fetch(CHATBOT_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    const reply = (data && typeof data.reply === 'string')
                        ? data.reply
                        : 'Sorry, I could not read the response. Please try again.';
                    const wantContact = (data && data.show_contact_form === true)
                        || /\b(human|agent|person|call\s*me|contact\s*form|leave\s+a\s+message|representative|talk\s+to\s+human)\b/i.test(message);
                    var bubbleId = 'adm-' + Date.now();
                    chatMessages.innerHTML += `
                        <div class="message admin">
                            <img src="images/img-1.jpg" class="avatar" alt="Admin Avatar">
                            <div class="text" id="${bubbleId}"></div>
                        </div>
                    `;
                    var el = document.getElementById(bubbleId);
                    if (reply.length >= 50) {
                        var i = 0;
                        var step = 2;
                        var delay = Math.max(10, Math.min(20, Math.floor(1600 / reply.length)));
                        function tick() {
                            if (!el) { return; }
                            if (i >= reply.length) {
                                el.innerHTML = escapeHtml(reply).replace(/\n/g, '<br>');
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                                if (wantContact) { showLegacyContactForm(); }
                                return;
                            }
                            i = Math.min(reply.length, i + step);
                            el.innerHTML = escapeHtml(reply.slice(0, i)).replace(/\n/g, '<br>');
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                            setTimeout(tick, delay);
                        }
                        tick();
                    } else {
                        el.innerHTML = escapeHtml(reply).replace(/\n/g, '<br>');
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        if (wantContact) { showLegacyContactForm(); }
                    }
                })
                .catch(function() {
                    chatMessages.innerHTML += `
                        <div class="message admin">
                            <img src="images/img-1.jpg" class="avatar" alt="Admin Avatar">
                            <div class="text">${escapeHtml('Sorry, the assistant is unreachable right now. Please try again.')}</div>
                        </div>
                    `;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
            }

            sendMessageButton.addEventListener('click', function() {
                postChatMessage(messageInput.value);
            });

            chatMessages.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(e.target);
                fetch('submit_message.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const responseForm = document.getElementById('response-form');
                    if (data.status === 'success') {
                        responseForm.innerHTML = '<p>Thank you for your message. We will get back to you soon!</p>';
                    } else {
                        responseForm.innerHTML = '<p>Error: ' + escapeHtml(String(data.message != null ? data.message : 'Unknown')) + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('response-form').innerHTML = '<p>There was an error submitting the form. Please try again.</p>';
                });
            });
        });
    </script>
</body>
</html>
