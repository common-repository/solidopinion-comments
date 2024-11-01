<?php 
use SolidOpinion\SOAuth;

function so_get_thread_url(){
    global $post;
    //$link_data = parse_url(get_permalink());
    //$so_thread = str_replace($link_data['scheme'].'://'.$link_data['host'], '', site_url()) . '/?p=' . $post->ID; 
    $so_thread = str_replace(home_url(), '', get_permalink()); 
    return $so_thread;
}

function so_get_shortname(){
    $so_option = get_option('so_options');
    if (!($so_option && isset($so_option['so_shortname']) && ($so_option['so_shortname']!=''))) return false;
    return $so_option['so_shortname'];
}

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

function get_language(){
    $lang_data = get_locale();
    if (isset($lang_data) && $lang_data){
        $langs = explode('_', $lang_data);
        if ($langs && isset($langs[0]) && $langs[0]){
            return strtolower($langs[0]);
        }
    }
    return 'en';
}

function get_so_language_id(){
    $languages_ids = array(
        'en' => 2,
        'ru' => 1,
        'ua' => 1,
        'uk' => 1
    );
    $current_lang = get_language();
    if (isset($languages_ids[$current_lang])){
        return $languages_ids[$current_lang];
    }
    return 2;
}

function register_so_community_widget() {
    register_sidebar_widget(__('SolidOpinion Community', 'solidopinion-comments'), 'so_community_widget');
    wp_register_sidebar_widget(
        'so_widget_1',
        __('SolidOpinion Community', 'solidopinion-comments'),
        'so_community_widget',
        array(                  // options
            'description' => __('SolidOpinion Community Widget', 'solidopinion-comments')
        )
    );
}

function so_get_comments_number($anchor='#comments') {
    $so_shortname = so_get_shortname();
    if (!$so_shortname ) {
        return;
    }    
    $return = is_home() ? str_replace(array('%%SO_SITENAME%%', '%%SO_THREAD_URL%%'), array($so_shortname, so_get_thread_url()), get_include_contents(SO_COMMENTS_DIR . '/templates/counter_template.php')) : '';
    return $return;
}
 
    
function so_comment_template($comment_template) {
    global $post;
    $so_shortname = so_get_shortname();
    if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) || !$so_shortname ) {
        return;
    }

    $sso_public_key = get_option('sso_public_key');
    $sso_encryption_key = get_option('sso_encryption_key');
    $sso_signing_key = get_option('sso_signing_key');
    
    if (!empty($sso_public_key) && !empty($sso_encryption_key) && !empty($sso_signing_key)) {
        require_once(SO_COMMENTS_DIR . '/lib/soauth/soauth.php');

        $current_user = wp_get_current_user();
        $payload = [
            'user' => $current_user->user_login,
            'email' => $current_user->user_email,
            'avatar' => ''
        ];
        $soAuth = new SOAuth($sso_encryption_key, $sso_signing_key, $sso_public_key);
        update_option('so_sso_data', $soAuth->encrypt($payload));
    }
    return SO_COMMENTS_DIR . '/templates/comments_template.php';
}

function so_comments_uninstall()
{
    if (!current_user_can('activate_plugins')) return;

    //if ( __FILE__ != WP_UNINSTALL_PLUGIN ) return;
    
    $so_option = get_option('so_options');
    if (isset($so_option) && $so_option){
        delete_option('so_options');
    }

}
