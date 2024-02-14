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
        $infor_url = $last_data->uniq_url;
        $host = explode("/", $infor_url);
        $botname = $host[2];
        $api_url = 'https://api.openai.com/v1/chat/completions';
        $api_key =  OPENAI_API_KEY;
        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        );
        $model = esc_attr(get_option('chatbot_chatgpt_model_choice', 'gpt-3.5-turbo-16k'));
        $prompt = "Hi, good morning. My name is $botname. I am a virtual assistant for your WordPress website. I have a lot of information about this website. I have very good knowledge about the $htmlContent. However, I cannot answer non related  questions in  $htmlContent. In such cases, my response will be, 'I cannot answer your questions. I don't know about that.' I can provide very deep answers In that time i will  response Please visit $infor_url for more informations. Have a nice day.";
        $data = array(
            'model' => $model,
            'messages' => array(
                array('role' => 'system', 'content' => $prompt),
                array('role' => 'user', 'content' => $user_input),
            ),
            "temperature" => 0.75
        );

        $data_json = json_encode($data);
        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body' => $data_json,
        )
        );
        if (is_wp_error($response)) {
            return "Error: " . $response->get_error_message();
        } else {
            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);
            $result['url'] = $infor_url;
            $result['x']=get_stylesheet_directory_uri();
            return $result;
        }
    }
}
?>