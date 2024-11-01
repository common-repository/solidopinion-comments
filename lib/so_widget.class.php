<?php
class so_community_widget extends WP_Widget {

    // constructor
    function __construct() {
        parent::__construct(false, $name = __('SolidOpinion Community', 'solidopinion-comments') );
    }

    // widget form creation
    function form($instance) {	
        // Check values
        echo "<p>You can manage this widget from <a href='".admin_url('options-general.php?page=so_comments')."'>SolidOpinion publisher panel</a></p>";
    }

    // widget display
    function widget($args, $instance) {  
        $so_shortname = so_get_shortname();
        if (!$so_shortname ) {
            return;
        }
        $lang_id = get_so_language_id();
        $url = SO_API_URL.'api/Site/getpublic/?shortname='.$so_shortname;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "dev:eQ9UmN6WsuY");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $exec = curl_exec($curl);
        $response = json_decode($exec,true);
        curl_close($curl);

        if (!$exec || (!empty($response) && isset($response['total_messages']) && $response['total_messages'] > 0)) {
          echo $before_widget;
          echo $before_title;
          echo __('Community', 'solidopinion-comments');
          echo $after_title;
          echo str_replace(array('%%SO_SITENAME%%'), array($so_shortname), get_include_contents(SO_COMMENTS_DIR . '/templates/community_template.php'));
          echo $after_widget; 
        }
    }
}

