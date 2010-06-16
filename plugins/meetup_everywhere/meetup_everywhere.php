<?php

/*
Plugin Name: Meetup.Everywhere
Plugin URI: http://blog.nydd.org/2010/05/wordpress-plugin-for-meetup-everywhere/
Description: Meetup Everywhere
Author: Takashi Mizohata
Version: 0.1
Author URI: http://www.google.com/profiles/beatak
 */

class MeetupEverywhere extends WP_Widget {

  const API_BASE = 'http://api.meetup.com/';
  const NAME = 'MeetupEverywhere';
  const CAPABILITY = '9';
  const WORDPRESS_OPTION_KEY = 'meetup_everywhere_settings';

  public static $settings;
  public static $is_showable = false;
  public static $OPTION_INPUT_KEYS = array('api_key', 'name', 'description', 'link', 'link_name', 'facebook_urlname', 'twitter_urlname', 'event_create', 'scheduling', 'container_id');

  /** constructor */
  function MeetupEverywhere() {
    //error_log('MeetupEverywhere::Constructor');
    parent::WP_Widget(false, $name = 'MeetupEverywhere');
    self::$settings = get_option(MeetupEverywhere::WORDPRESS_OPTION_KEY);
    if (!empty(self::$settings['api_key']) && !empty(self::$settings['container_id'])) {
      self::$is_showable = true;
    }
  }

  /** Display widget instance
   * 
   * using extract(), it seems, to me, not be a good idea, 
   * but also seems very conventional.
   * echo() from this method is rendered on the page.
   *
   * @param array $args displaying arguments name, id, [before|after]_[widget|title], widget_[id, name]
   * @param array $inst setting of each instance
   */
  function widget ($args, $inst) {
    //error_log('MeetupEverywhere::widget');
    /*
    $is_tag_exists = function_exists('is_tag');
    $mega_tags     = array();
    if (is_single()) {
      $the_tags = get_the_tags(get_the_ID());
      foreach ($the_tags as $tag) {
        array_push($mega_tags, $tag->term_id);
      }
    }

    extract($args);
    $tags       = get_tags(array('orderby' => '', 'hide_empty' => false));
    $tag_length = count($tags);
    $i          = 1;
    //error_log(print_r($tags, true));
    if (strlen($inst['title'])) {
      echo $before_title . $inst['title'] . $after_title;
    }
    */
    echo $before_widget;
    if (self::$is_showable) {
      $events = get_events(self::$settings['api_key'], self::$settings['container_id']);
      include('html/widget.php');
    }
    else {
      echo "NOT configured";
    }
    echo $after_widget; 
  }

/** 
  @see WP_Widget::update
  Leave it as it is.  Parent class should take care of it. 
*/
/*
  function update ($new_instance, $old_instance) {
    //error_log('MeetupEverywhere::update');
    return $new_instance;
  } 
*/

  /** @see WP_Widget::form */
  function form ($inst) {
    //error_log('MeetupEverywhere::form');
    include('html/form.php');
  }

  public static function getSettings () {
    return get_option(MeetupEverywhere::WORDPRESS_OPTION_KEY);
  }

  public static function setSettings ($obj) {
    update_option(MeetupEverywhere::WORDPRESS_OPTION_KEY, $obj);
  }
}

//
// ___________________________________________________________________
// ___________________________________________________________________
// ___________________________________________________________________
//

function get_events ($api_key, $container_id) {
  $result = null;
  $url_list = MeetupEverywhere::API_BASE . 'ew/events/?key=' . $api_key . '&container_id=' . $container_id;
  // error_log($url_list);
  $http = new WP_Http();
  $resp = $http->request($url_list);
  $obj_list = json_decode($resp['body']);
  //error_log(print_r($obj_list, true));
  if (!empty($obj_list->results)) {
    $result = $obj_list->results;
  }
  return $result;
}

function get_container_id ($api_key, $name) {
  $urlname = str_replace(' ', '-', $name);
  $url_list = MeetupEverywhere::API_BASE . 'ew/containers/?key=' . $api_key . '&urlname=' . $urlname;
  $http = new WP_Http();
  $resp = $http->request($url_list);
  $obj_list = json_decode($resp['body']);
  //error_log(print_r($obj_list, true));
  $result = null;
  if (!empty($obj_list->results) && !empty($obj_list->results[0]->id)) {
    $result = $obj_list->results[0]->id;
  }

  return $result;
}

function widget_MeetupEverywhere_init () {
  return register_widget('MeetupEverywhere');
}

function widget_MeetupEverywhere_option () {
  if (function_exists('add_submenu_page')) {
    add_submenu_page(
      'options-general.php',
      'Meetup Everywhere',
      'Meetup Everywhere',
      MeetupEverywhere::CAPABILITY, 
      __FILE__,
      'widget_MettupEverywhere_configuration'
    );
  }
}

function widget_MettupEverywhere_configuration () {
  //error_log('POST: ' . print_r($_POST, true));
  if (!empty($_POST)) {
    $settings = array();
    foreach (MeetupEverywhere::$OPTION_INPUT_KEYS as $key) {
      //error_log($key);
      if (!empty($_POST[$key])) {
        $settings[$key] = trim($_POST[$key]);
      }
    }
    //error_log('settings: ' . print_r($settings, true));

    if (empty($settings['container_id']) && !empty($settings['name'])) {
      $settings['container_id'] = get_container_id($settings['api_key'], $settings['name']);
    }
    // error_log('settings: ' . print_r($settings, true));

    MeetupEverywhere::setSettings($settings);
  }
    
  include('html/options.php');
}

/**
 * Method: get
 */
function container_create () {
  // this is how you get access to the database
  global $wpdb;

  $is_avail = false;
  $mue_settings = get_option(MeetupEverywhere::WORDPRESS_OPTION_KEY);
  //error_log(print_r( $_POST, true));
  $mue_name = $_POST['mue_name'];
  $urlname = str_replace(' ', '-', $mue_name);
  //error_log($urlname);
  //error_log(print_r($mue_settings, true));
  $http = new WP_Http();

  // get list
  $url_list = MeetupEverywhere::API_BASE . 'ew/containers/?key=' . $mue_settings['api_key'] . '&urlname=' . $urlname;
  $resp = $http->request($url_list);
  $obj_list = json_decode($resp['body']);  
  //error_log(print_r($obj_list, true));

  if (count($obj_list->results) > 0) {
    // check if the mue is yours
    $url_self = MeetupEverywhere::API_BASE . 'members/?key=' . $mue_settings['api_key'] . '&relation=self';
    $resp = $http->request($url_self);
    $obj_self = json_decode($resp['body']);
    //error_log(print_r($obj_self, true));

    if ($obj_list->results[0]->founder->member_id == $obj_self->results[0]->id) {
      //error_log('This meetup belongs to you');
      $is_avail = true;
    }
    else {
      //error_log('Not yours');
    }
  }
  else {
    // try to create it
    $url_create = MeetupEverywhere::API_BASE . 'ew/container/';
    $resp = $http->request(
      $url_create,
      array(
        'method' => 'POST',
        'body' => array(
          'key' => $mue_settings['api_key'],
          'name' => $mue_name
        )
      )
    );
    //error_log(print_r($resp, true));
    if ($http['response']['code'] == '201') {
      $obj_create = json_decode($http['body']);
    }
    else {
      error_log('Hmm, server returned error code. But most likely it is made.');
    }
    $is_avail = true;
  }

  echo ($is_avail ? 'true' : 'null');
  die();
}

add_action('widgets_init', 'widget_MeetupEverywhere_init');
add_action('admin_menu', 'widget_MeetupEverywhere_option' );

// Ajax call
add_action('wp_ajax_container_create', 'container_create');
