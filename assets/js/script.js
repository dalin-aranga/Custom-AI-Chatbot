jQuery(document).ready(function ($) {
    $('#chatbot-icon').on('click', function () {
        openChatWindow();
       
    });
     
    function openChatWindow() {
        
        var themeColors = getThemeColors();
        $('body').append(`
        <div class="chat-modal"  style="
        color: ${themeColors.text};
        background-color: ${themeColors.background};
        border-color: ${themeColors.border};
    ">
    <div class="chat-content">
    <div class="chatbox__header">
        <div class="chatbox__image--header">
            <img src="https://img.icons8.com/color/48/000000/circled-user-female-skin-type-5--v1.png" alt="image">
        </div>

        <div class="chatbox__content--header">
            <h4 class="chatbox__heading--header">🤖How can I help you today?🤖</h4>
            
        </div>
    </div>
    <div class="chat-body" id="chat-messages">
    <p Hi. My name is Defyn. How can I help you?</p>
    </div>
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

    function clearCacheAndCookies() {
        window.location.reload(true); 
        var cookies = document.cookie.split(";");
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];
            var eqPos = cookie.indexOf("=");
            var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
        }
    }
    
    function getThemeColors() {
        var themeColors = {
            text: 'inherit',       
            background: 'inherit',
            border: 'inherit'      
        };
        themeColors.text = $('.example-text').css('color');
        themeColors.background = $('.example-background').css('background-color');
        themeColors.border = $('.example-border').css('border-color');

        return themeColors;
    }

function sendMessage() {
    var userInput = $('#user-input').val();
        if (userInput.trim() !== '') {
            $('#chat-messages').append(`<div class="user-message">${userInput}</div>`);
            var ajaxUrl = chatbot_params.ajax_url + '?timestamp=' + new Date().getTime();
            $.ajax({
                type: 'POST',
                url: ajaxUrl, 
                data: {
                    action: 'process_user_input',
                    user_input: userInput,
                },
                success: function (response) {
                    var cleared_response = response.data.bot_response.choices[0].message.content.trim();
                    // var url = response.data.bot_response['url'];
                    // var modified_output = `${cleared_response} <br> More information 👉 <a href="${url}" target="_blank">click </a>`;
                    // Append the bot's response to the chat body
                    $('#chat-messages').append(`<div class="bot-message">${cleared_response}</div>`);
                },
                error: function(error) {
                    console.error('Error sending user input:', error);
                    clearCacheAndCookies();
                }
            });
            console.log("Message sent");
            // Clear the input field
            $('#user-input').val('');
        }
    }

  

});
