<?php
/*
Plugin Name: SolidOpinion Comments
Description: Implement SolidOpinion comments features
Version: 2.0
Author: SolidOpinion Team
Author URI: http://solidopinion.com/
Plugin URI: http://solidopinion.com/
Text Domain: solidopinion-comments
Domain Path:   /locales/
*/

define('SO_BACKEND_URL', '//my.solidopinion.com/');
define('SO_API_URL','//api.solidopinion.com/');
define('SO_SERVER','');



define('SO_COMMENTS_DIR', plugin_dir_path(__FILE__));
define('SO_COMMENTS_URL', plugin_dir_url(__FILE__));

define('HELP_EMAIL','help@solidopinion.com');
define('INTEGRATION_EMAIL','integration@solidopinion.com');


function so_load_textdomain() {
    load_plugin_textdomain('solidopinion-comments', false, basename( dirname( __FILE__ ) ) . '/locales');
}

function so_settings_warning() {
    $so_option = get_option('so_options');
    if (!$so_option || !$so_option['so_shortname']) {
      echo '<div class="error"><p style="font-size: 14px;"><strong>'.sprintf(__('Please %sconfigure%s plugin to enable SolidOpinion Comments on your site.', 'solidopinion-comments'), '<a href="'.get_settings('siteurl').'/wp-admin/options-general.php?page=so_comments">', '</a>').'</strong></p></div>';
    }
}

add_action( 'plugins_loaded', 'so_load_textdomain' );

require_once(SO_COMMENTS_DIR . '/lib/common.php');
require_once(SO_COMMENTS_DIR . '/lib/smtp.class.php');
require_once(SO_COMMENTS_DIR . '/lib/settings_page.class.php');
require_once(SO_COMMENTS_DIR . '/lib/so_widget.class.php');

register_uninstall_hook(__FILE__, 'so_comments_uninstall');

function so_comments_settings_link($actions, $file) {
    if (false !== strpos($file, 'solidopinion-comments'))
        $actions['settings'] = '<a href="options-general.php?page=so_comments">' . __('Settings', 'solidopinion-comments') . '</a>';
    return $actions;
}

add_filter('plugin_action_links', 'so_comments_settings_link', 2, 2);

if( is_admin() ){
    $my_settings_page = new MySettingsPage();
}

add_filter("comments_template", "so_comment_template");
add_filter("comments_number", "so_get_comments_number");
add_action('widgets_init', create_function('', 'unregister_widget("WP_Widget_Recent_Comments"); return register_widget("so_community_widget");'));
