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

            // Toggle chat window
            chatWidget.addEventListener('click', function() {
                chatWindow.style.display = 'flex';
                chatWidget.style.display = 'none';
            });

            closeButton.addEventListener('click', function() {
                chatWindow.style.display = 'none';
                chatWidget.style.display = 'flex';
            });

            sendMessageButton.addEventListener('click', function() {
                const message = messageInput.value.trim();
                if (message) {
                    chatMessages.innerHTML += `
                        <div class="message user">
                            <div class="text">${message}</div>
                        </div>
                    `;

                    messageInput.value = '';

                    // Simulate a delay for admin response
                    setTimeout(function() {
                        chatMessages.innerHTML += `
                            <div class="message admin">
                                <img src="images/img-1.jpg" class="avatar" alt="Admin Avatar">
                                <div class="text">
                                    Sorry to keep you waiting, unfortunately all of our agents are currently busy or away, 
                                    please leave a message and we will get back to you as soon as possible. What’s the best 
                                    email to reach you on?
                                </div>
                            </div>
                        `;
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
                        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the bottom
                    }, 1000);
                }
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
                        responseForm.innerHTML = `<p>Error: ${data.message}</p>`;
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
