<?php

$settings = MeetupEveryWhere::getSettings();
//error_log(print_r($settings, true));
$is_visible = (empty($settings['api_key']) ? false : true);

?>

<h1>Configuration!</h1>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">


<div class="">
  <label for="mu_api_key">Meetup API key</label>
  <input id="mu_api_key" type="text" name="api_key" value="<?php echo htmlspecialchars($settings['api_key'], ENT_NOQUOTES);?>" />
</div>

  <?php if ($is_visible) {?>

<div class="">
    <div clas="error_display" id="error_url_name"></div>
    <label for="mue_name">Meetup Name</label>
    <input id="mue_name" type="text" name="name" value="<?php echo htmlspecialchars($settings['name'], ENT_NOQUOTES);?>" />

<a href="#" id="mue_create_meetup">Create Meeup</a>
</div>

   <div id="mue_additional_info" <?php if (empty($settings['container_id'])) {?>style="display:none;"<?php }?>>

<div class="">
      <div clas="error_display" id="error_description"></div>
      <label for="mue_description">Description</label>
      <input id="mue_description" type="text" name="description" value="<?php echo htmlspecialchars($settings['description'], ENT_NOQUOTES);?>" />
</div>

<div class="">
      <div clas="error_display" id="error_link"></div>
      <label for="mue_link">Link URL</label>
      <input id="mue_link" type="text" name="link" value="<?php echo htmlspecialchars( (!empty($settings['link']) ? $settings['link'] : get_bloginfo('wpurl')), ENT_NOQUOTES);?>" />
</div>

<div class="">
      <div clas="error_display" id="error_link_name"></div>
      <label for="mue_link_name">Link Title</label>
      <input id="mue_link_name" type="text" name="link_name" value="<?php echo htmlspecialchars( (!empty($settings['link_name']) ? $settings['link_name'] : get_bloginfo('name')), ENT_NOQUOTES);?>" />
</div>

<div class="">
      <div clas="error_display" id="error_facebook_urlname"></div>
      <label for="mue_facebook_urlname">Facebok</label>
      <input id="mue_facebook_urlname" type="text" name="facebook_urlname" value="<?php echo htmlspecialchars($settings['facebook_urlname'], ENT_NOQUOTES);?>" />
</div>

<div class="">
      <div clas="error_display" id="error_twitter_urlname"></div>
      <label for="mue_twitter_urlname">Twitter</label>
      <input id="mue_twitter_urlname" type="text" name="twitter_urlname" value="<?php echo htmlspecialchars($settings['twitter_urlname'], ENT_NOQUOTES);?>" />
</div>

<div class="">
      <div clas="error_display" id="error_event_create"></div>
      <label for="mue_event_create">Who can create Event?</label>
      <input id="mue_event_create" type="text" name="event_create" value="<?php echo htmlspecialchars($settings['event_create'], ENT_NOQUOTES);?>" />
             : "founder" or "anyone"
</div>

<div class="">
      <div clas="error_display" id="error_scheduling"></div>
      <label for="mue_scheduling">How it can be scheduled?</label>
      <input id="mue_scheduling" type="text" name="scheduling" value="<?php echo htmlspecialchars($settings['scheduling'], ENT_NOQUOTES);?>" />
      "open" (no effect), "date_locked", or "time_locked"
</div>

  </div>

  <?php } ?>

   <input id="mue_container_id" type="hidden" name="container_id" value="<?php echo htmlspecialchars($settings['container_id'], ENT_NOQUOTES);?>" />

  <input type="submit" />
</form>

<script type="text/javascript">
var create_url_name = function (mue_name) {
    jQuery.ajax(
        {
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'container_create',
                mue_name: mue_name
            },
            success: function (response) {
                if (response == 'true') {
                    jQuery('#mue_additional_info').css('display', 'block');
                }
                else {
                    alert(mue_name + ' is used alaredy');
                }
            }
        }
    );
};

jQuery(document).ready(
    function ()  {
        jQuery('#mue_create_meetup').click(
            function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                create_url_name(jQuery('#mue_name').attr('value'));
                return false;
            }
        );
    }
);
</script>