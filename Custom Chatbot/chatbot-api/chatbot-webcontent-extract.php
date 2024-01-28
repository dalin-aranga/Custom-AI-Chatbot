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
    
        // point to the body, then get the innertext
        $data = str_get_html($html)->find('body', 0)->innertext;
    
        // remove HTML tags
        $plaintext = strip_tags($data);
        return $plaintext;
    }
}

$obj = new ChatbotWebContentExtract();
$content= $obj->fetdata("https://defyndigitadev.wpenginepowered.com/"); 
echo $content;

?>