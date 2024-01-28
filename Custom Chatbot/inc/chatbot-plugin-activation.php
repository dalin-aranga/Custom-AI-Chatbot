<?php
/**
 * @package  ChatbotPlugin
 */

class ChatbotPluginActivation
{
	public static function activation() {
		flush_rewrite_rules();
	}
}