<?php
//add jquery for gravity forms
add_filter('gform_register_init_scripts', 'gform_addScript');
function gform_addScript($form) {   
  $script = "var options = {minutesInterval: 15 }; "
          . "jQuery('.timepicker').wickedpicker(options);";
          
  GFFormDisplay::add_init_script($form['id'], 'formScript', GFFormDisplay::ON_PAGE_RENDER, $script);

  return $form;
}