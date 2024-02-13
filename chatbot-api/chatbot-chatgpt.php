<?php

/**
 * @package  ChatbotPlugin
 */

 global $wpdb;

class ChatbotChatgpt
{
    public static function callAPi($user_input)
    {
        global $wpdb;
        $result= $wpdb->get_results("SELECT * FROM {$wpdb->prefix}webdata001 ORDER BY ID DESC LIMIT 1");
        $last_data = $result[0];
        $htmlContent = $last_data->web_content;


        // Current ChatGpt API end Point
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $api_key = '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'; //  OpenAI API key

        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        );
        $model = esc_attr(get_option('chatbot_chatgpt_model_choice', 'gpt-3.5-turbo-16k'));

        // Prepare the prompt for GPT-3.5-turbo
        $prompt = "Extracted information from the website:\n$htmlContent\n\nAsk me any questions or provide equations, and I will do my best to answer.";


        // Prepare data for API request
        $data = array(
            'model' => $model,
            'temperature' => 0.5,
            'messages' => array(
                array('role' => 'system', 'content' => 'You are a helpful assistant.'),
                array('role' => 'user', 'content' => $prompt),
                // Add an additional message for equation context
                array('role' => 'user', 'content' => $user_input), // You can replace '7 + 3' with an actual equation input
            ),
        );

        $data_json = json_encode($data);

        // Send request to OpenAI API
        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body' => $data_json,
        )
        );

        // Handle the response as needed
        if (is_wp_error($response)) {
            // Handle error
            return "Error: " . $response->get_error_message();
        } else {
            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);
            $result['url'] = $last_data->uniq_url;
            return $result;
        }
    }
}


?>