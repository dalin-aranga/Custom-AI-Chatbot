jQuery(document).ready(function($) {
    $('#chatbot-icon').on('click', function() {
        openChatWindow();
    });

    function openChatWindow() {
        $('body').append(`
        <div class="chat-modal">
            <div class="chat-content">
                <div class="chat-header">Virtual Assistant</div>
                <div class="chat-body" id="chat-messages"></div>
                <div class="chat-footer">
                    <input type="text" id="user-input" placeholder="Type your message...">
                    <button id="send-button">Send</button>
                    <button class="close-chat">Close</button>
                </div>
            </div>
        </div>
    
        `);

        // Close chat button
        $('.close-chat').on('click', function() {
            $('.chat-modal').remove();
        });

        // Send button
        $('#send-button').on('click', function() {
            sendMessage();
        });

        // Handle Enter key press
        $('#user-input').on('keypress', function(e) {
            if (e.which === 13) {
                sendMessage();
            }
        });
    }

function sendMessage() {
        var userInput = $('#user-input').val();
        if (userInput.trim() !== '') {
            // Append user's message to the chat body
            $('#chat-messages').append(`<div class="user-message">${userInput}</div>`);
            
            $.ajax({
                type: 'POST',
                url: chatbot_params.ajax_url, 
                data: {
                    action: 'process_user_input',
                    user_input: userInput,
                },
                success: function (response) {
                    console.log(response)
                    var botResponse = response.data.bot_response;
                    // Append the bot's response to the chat body
                    $('#chat-messages').append(`<div class="bot-message">${botResponse}</div>`);
                },
                error: function(error) {
                    console.error('Error sending user input:', error);
                }
            });
            
            // Clear the input field
            $('#user-input').val('');
        }
    }
});



// Add the following code at the end to close the chat on clicking outside the modal
$(document).on('click', function(e) {
    if (!$(e.target).closest('.chat-modal').length && !$(e.target).is('#chatbot-icon')) {
        $('.chat-modal').remove();
    }
});
