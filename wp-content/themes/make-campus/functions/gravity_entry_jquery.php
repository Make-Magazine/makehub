<?php
//add jquery for gravity forms
add_filter('gform_register_init_scripts', 'gform_addScript');
function gform_addScript($form) {
  $script = 'jQuery( ".drop_down_time input[type=text]" ).each(function() {          
      var parent_class = jQuery(this).parent().attr("class");                  
      var thisAttrID = jQuery(this).attr("id");
      jQuery("#"+thisAttrID).hide();
      var eleValue = this.value;
      
      if(parent_class.includes("gfield_time_hour")){                
        var optArray =  ["", "1", "2", "3","4", "5","6","7","8","9","10","11","12"];
      }else if(parent_class.includes("gfield_time_minute")){                
        var optArray = ["", "00", "15", "30", "45"];
      }
      
      jQuery(this).after("<select id=\'override_"+thisAttrID+"\' class=\'hour_time_override\'></select");
        jQuery.each(optArray, function (i, item) {
            var selected="";
            if(eleValue==item) {
                selected="selected";
            }
            jQuery("#override_"+thisAttrID).append("<option "+selected+">"+item+"</option>");
        });
    });
    
    jQuery(".hour_time_override").on(\'change\', function() {
        var eleID = jQuery(this).attr("id");
        eleID = eleID.replace("override_","");    

        jQuery("#"+eleID).val(this.value);
    });';

  GFFormDisplay::add_init_script($form['id'], 'formScript', GFFormDisplay::ON_PAGE_RENDER, $script);

  return $form;
}