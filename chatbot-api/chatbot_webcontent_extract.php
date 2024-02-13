<?php

/**
 * @package  ChatbotPlugin
 */

class ChatbotWebContentExtract{
    public static  function fetdata($url) {
        include 'simple_html_dom.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        $htmlObject = str_get_html($html);

        if ($htmlObject) {
            $data = $htmlObject->find('body', 0)->innertext;
            $plaintext = strip_tags($data);
            return $plaintext;
        } else {
            // Handle the case where HTML parsing fails
            return "Error parsing HTML";
        }
            
        // remove HTML tags
        $plaintext = strip_tags($data);
        return $plaintext;
    }
}
?>