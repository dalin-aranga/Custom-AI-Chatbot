<?php

/**
 * @package  ChatbotPlugin
 */



class ChatbotChatgpt{
    public static function callAPi($user_input){
        require_once plugin_dir_path(__FILE__) .'../chatbot-plugin.php';
        $base_url = ChatBotPlugin::get_base_url();

        require_once plugin_dir_path(__FILE__) .'chatbot-webcontent-extract.php';
        $htmlContent = ChatbotWebContentExtract::fetdata($base_url);

        // Current ChatGpt API end Point
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $api_key = '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'; //  OpenAI API key

        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        );
        $model = esc_attr(get_option('chatbot_chatgpt_model_choice', 'gpt-3.5-turbo'));
        $max_tokens = intval(esc_attr(get_option('chatbot_chatgpt_max_tokens_setting', '30')));

        // Prepare the prompt for GPT-3.5-turbo
        $prompt = "Extracted information from the website:\n$htmlContent\n\nAsk me any questions or provide equations, and I will do my best to answer.";


        // Prepare data for API request
        $data = array(
            'model' => $model,
            'max_tokens' => $max_tokens,
            'temperature' => 0.5,
            'messages' => array(
                array('role' => 'system', 'content' => 'You are a helpful assistant.'),
                array('role' => 'user', 'content' => $prompt),
                // Add an additional message for equation context
                array('role' => 'user', 'content' =>  $user_input), // You can replace '7 + 3' with an actual equation input
            ),
        );

        $data_json = json_encode($data);

        // Send request to OpenAI API
        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body' => $data_json,
        ));

        // Handle the response as needed
        if (is_wp_error($response)) {
            // Handle error
            return "Error: " . $response->get_error_message();
        } else {
            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);

            // Extract and use the generated response from GPT-3.5-turbo
            $generated_response = $result['choices'][0]['message']['content'];

            $url = 'http://www.example.com/';
            $formatted_response = "'Generated Answer': $generated_response\n'Sources': $url";

            // Output or use the generated_response as needed
            return $formatted_response;
        }
    }
}

?>