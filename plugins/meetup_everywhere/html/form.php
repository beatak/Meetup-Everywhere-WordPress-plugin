<?php

$title = esc_attr($inst['title']);
$width = esc_attr($inst['width']);
$height = esc_attr($inst['height']);
$zoom = esc_attr($inst['zoom']);

?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
<?php 
  _e('Title:'); 
?>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
  </label>
</p>

<p>
  <label for="<?php echo $this->get_field_id('width'); ?>">
<?php 
  _e('Width:'); 
?>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" />
  </label>
</p>

<p>
  <label for="<?php echo $this->get_field_id('height'); ?>">
<?php 
  _e('Height:'); 
?>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" />
  </label>
</p>

<p>
  <label for="<?php echo $this->get_field_id('zoom'); ?>">
<?php 
  _e('Zoom:'); 
?>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name('zoom'); ?>" value="<?php echo $zoom; ?>" />
  </label>
</p>