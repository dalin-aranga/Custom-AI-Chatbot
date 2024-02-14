<?php
/*
Plugin Name: ChatBot Plugin
Description: Adds a chatbot icon to your website.
Version: v.001
Author: Perera MDA
*/

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

if ( !class_exists( 'ChatBotPlugin' ) ) {
    class ChatBotPlugin {

        public $plugin;
        public function __construct() {
            $this->plugin = plugin_basename( __FILE__ );
            add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );
            add_filter( "plugin_action_links_$this->plugin", array( $this, 'edit_link' ) );
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));
            register_uninstall_hook(__FILE__,'uninstall');
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
            add_action('wp_ajax_process_user_input', array($this, 'process_user_input'));
            add_action('wp_ajax_nopriv_process_user_input',  array($this, 'process_user_input'));
            add_action('wp_footer', array($this, 'addIcon'));
            add_action('admin_menu', array($this, 'add_admin_pages'));
            add_filter( 'cron_schedules', array( $this, 'custom_every_three_hours_schedule' ) );
            add_action('init', array($this, 'schedule_cron'));
        }
        public function schedule_cron() {
            if (!wp_next_scheduled('custom_every_three_hours_event')) {
                wp_schedule_event(time(), 'everythreehours', 'custom_every_three_hours_event');
            }
        }

        public function process_user_input() {
            $user_input = sanitize_text_field($_POST['user_input']);
            require_once plugin_dir_path(__FILE__) . '/chatbot-api/chatbot-chatgpt.php';
            $bot_response = ChatbotChatgpt::callAPi($user_input);
            wp_send_json_success(array('bot_response' => $bot_response));
        }
        public function settings_link( $links ) {
			$settings_link = '<a href="admin.php?page=chatbot_plugin">Settings</a>';
			array_push( $links, $settings_link );
			return $links;
		}
        public function edit_link( $links ) {
			$edit_link = '<a href="admin.php?page=chatbot_plugin">Edit</a>';
			array_push( $links, $edit_link );
			return $links;
		}
        public function activate() {
            require_once plugin_dir_path(__FILE__) . '/inc/chatbot-plugin-activation.php';
            ChatbotPluginActivation::activation();

            global $wpdb;
            $table_name = $wpdb->prefix . 'webdata001';
    
            $sql = "CREATE TABLE $table_name (
                id INT NOT NULL AUTO_INCREMENT,
                uniq_url VARCHAR(255) NOT NULL,
                web_content TEXT NOT NULL,
                PRIMARY KEY (id)
            ) ";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        function custom_every_three_hours_cronjob() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'webdata001';
            $base_url = get_bloginfo('url');
            require_once plugin_dir_path(__FILE__) . '/chatbot-api/chatbot_webcontent_extract.php';
            $htmlContent = ChatbotWebContentExtract::fetdata($base_url);
        
            if ($htmlContent) {
                $data = array(
                    'uniq_url'     => $base_url,
                    'web_content'  => $htmlContent,
                );
        
                $wpdb->insert($table_name, $data);
            } else {
                error_log('Failed to fetch web data from ' . $base_url);
            }
        }
        custom_every_three_hours_cronjob();

        }
        public function deactivate() {
            require_once plugin_dir_path(__FILE__) . '/inc/chatbot-plugin-deactivation.php';
            ChatbotPluginDeactivation::deactivation();
        }
        

        public static function uninstall() {
            require_once plugin_dir_path(__FILE__) . 'chatbot-uninstall.php';
            ChatbotPluginUninstall::uninstall();
        }
        public function enqueueScripts() {
            wp_enqueue_style('chatbot-styles', plugins_url('/assets/css/style.css', __FILE__));
            wp_enqueue_script('chatbot-script', plugins_url('/assets/js/script.js', __FILE__), array('jquery'), null, true);
            wp_localize_script('chatbot-script', 'chatbot_params', array('ajax_url' => admin_url('admin-ajax.php'),));
        }
        public function add_admin_pages() {
			add_menu_page( 'Chatbot plugin', 'Chatbot', 'manage_options', 'chatbot_plugin', array( $this, 'admin_index' ), 'dashicons-store', 110 );
		}
        public function admin_index(){
            require_once plugin_dir_path(__FILE__) . '/templates/admin.php';
        }
        public function addIcon() {
            ?>
            <div id="chatbot-icon">
            <img src="<?php echo plugins_url('/assets/images/chatbot-icon.png', __FILE__); ?>" alt="ChatBot Icon">
            </div>
            <?php
        }
    public function custom_every_three_hours_schedule( $schedules ) {
        $schedules['everythreehours'] = array(
            'interval' => 3 * 60 * 60, 
            'display'  => __( 'Custom Every Three Hours', 'gca-core' ),
        );
        return $schedules;
    }
  }

    $chatBotPlugin = new ChatBotPlugin();
}




