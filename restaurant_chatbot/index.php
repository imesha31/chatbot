<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Chatbot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f5;
        }
        .chat-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }
        .chat-box {
            height: 500px;
            overflow-y: auto;
            background-color: white;
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            max-width: 75%;
        }
        .user-message {
            background-color: #e3f2fd;
            margin-left: auto;
            text-align: right;
        }
        .bot-message {
            background-color: #f1f1f1;
            margin-right: auto;
        }
        .input-group {
            margin-top: 15px;
        }
        .restaurant-header {
            text-align: center;
            margin-bottom: 30px;
            color: #5c4033;
        }
        .menu-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="restaurant-header">
            <h1>Bella Cucina</h1>
            <p>Your friendly restaurant assistant</p>
        </div>
        
        <div class="chat-container">
            <div class="chat-box" id="chatBox">
                <!-- Chat messages will appear here -->
            </div>
            
            <div class="input-area">
                <div class="input-group">
                    <input type="text" id="userInput" class="form-control" placeholder="Type your message...">
                    <button class="btn btn-primary" id="sendBtn">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Add initial bot message
            addBotMessage("Hello! Welcome to Bella Cucina. May I know your name?");
            
            // Handle send button click
            $("#sendBtn").click(sendMessage);
            
            // Handle Enter key press
            $("#userInput").keypress(function(e) {
                if(e.which == 13) {
                    sendMessage();
                }
            });
            
            function sendMessage() {
                var userInput = $("#userInput").val().trim();
                
                if(userInput !== "") {
                    // Add user message to chat
                    addUserMessage(userInput);
                    
                    // Clear input field
                    $("#userInput").val("");
                    
                    // Send to backend and get response
                    $.ajax({
                        url: "chatbot.php",
                        type: "POST",
                        data: { message: userInput },
                        success: function(response) {
                            // Add bot response to chat
                            let botResponse = JSON.parse(response);
                            
                            if (Array.isArray(botResponse)) {
                                botResponse.forEach(msg => {
                                    if (typeof msg === 'object' && msg.type === 'menu') {
                                        addMenuItems(msg.items);
                                    } else {
                                        addBotMessage(msg);
                                    }
                                });
                            } else {
                                addBotMessage(botResponse);
                            }
                        },
                        error: function() {
                            addBotMessage("Sorry, there was an error processing your request.");
                        }
                    });
                }
            }
            
            function addUserMessage(message) {
                $("#chatBox").append(`
                    <div class="message user-message">
                        <div>${message}</div>
                    </div>
                `);
                scrollToBottom();
            }
            
            function addBotMessage(message) {
                $("#chatBox").append(`
                    <div class="message bot-message">
                        <div>${message}</div>
                    </div>
                `);
                scrollToBottom();
            }
            
            function addMenuItems(items) {
                let menuHtml = `
                    <div class="message bot-message" style="width: 90%;">
                        <div><strong>Our Menu:</strong></div>
                        <div class="menu-container">
                `;
                
                items.forEach(item => {
                    menuHtml += `
                        <div class="menu-item">
                            <strong>${item.name}</strong> - $${item.price}
                            <div>${item.description}</div>
                            <small>${item.category}</small>
                        </div>
                    `;
                });
                
                menuHtml += `
                        </div>
                    </div>
                `;
                
                $("#chatBox").append(menuHtml);
                scrollToBottom();
            }
            
            function scrollToBottom() {
                $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
            }
        });
    </script>
</body>
</html>
