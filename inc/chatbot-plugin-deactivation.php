<?php
/**
 * @package  ChatbotPlugin
 */

class ChatbotPluginDeactivation
{
	public static function deactivation() {
		flush_rewrite_rules();
	}
}