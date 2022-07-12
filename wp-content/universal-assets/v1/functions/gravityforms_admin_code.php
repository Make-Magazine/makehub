<?php
/* This adds new form types for the users to select when creating new gravity forms */
add_filter('gform_form_settings', 'my_custom_form_setting', 10, 2);

function my_custom_form_setting($settings, $form) {
   $form_type = rgar($form, 'form_type');

   if ($form_type == '')
      $form_type = 'basic'; //default

   //build select with all form type options
   $formTypes = array('basic'=>"Basic","SNM"=>"Science Near Me");
   $select = '<select name="form_type">';
   foreach ($formTypes as $key=>$result) {
      $select .= '<option value="' . $key . '" ' . ($form_type == $key ? 'selected' : '') . '>' . $result . '</option>';
   }
   $select .= '</select>';

   $settings[__( 'Form Basics', 'gravityforms' )]['form_type'] = '
    <tr>
      <th>Form Type</th>
      <td>' . $select . '</td></tr>';

   return $settings;
}

/* This will save the form type */
add_filter('gform_pre_form_settings_save', 'save_form_type_form_setting');

function save_form_type_form_setting($form) {
   $form['form_type'] = rgpost('form_type');
   return $form;
}

/*
 * function to allow easier testing of forms by skipping pages and going
 * directly to the page of the form you want to test
 * To skip a page, simply append the ?form_page=2 parameter to the URL of any
 * page on which you are displaying a Gravity Form
 */
add_filter("gform_pre_render", "gform_skip_page");

function gform_skip_page($form) {
   if (!rgpost("is_submit_{$form['id']}") && rgget('form_page') && is_user_logged_in())
      GFFormDisplay::$submission[$form['id']]["page_number"] = rgget('form_page');
   return $form;
}

/* This filter is used to correct the current form when in entry view.
 * When the entry id is set manually the form is not corrected
 */
add_filter('gform_admin_pre_render', 'correct_currententry_formid');

function correct_currententry_formid($form) {
   $current_page = (isset($_GET['page']) ? $_GET['page'] : '');
   $current_view = (isset($_GET['view']) ? $_GET['view'] : '');

   if ($current_page == 'gf_entries' && $current_view == "entry") {
      $current_formid = $_GET['id'];
      $current_entryid = (isset($current_entryid) ? $_GET['lid'] : 0);
      if ($current_entryid !== 0) {
         // Different form is in URL than in the form itself.
         global $wpdb;
         $result = $wpdb->get_results($wpdb->prepare("SELECT id,form_id from wp_gf_entry WHERE id=%d", $current_entryid));
         if ($result[0]) {
            if ($current_formid != $result[0]->form_id) {
               $form = GFFormsModel::get_form_meta(absint($result[0]->form_id));
            }
         }
      }
   }
   return $form;
}

//add new field to advanced setting section of form entry
add_action( 'gform_field_advanced_settings', 'my_advanced_settings', 10, 2 );
function my_advanced_settings( $position, $form_id ) {
  $form     = GFAPI::get_form($form_id);

  // add SNM input field only if form_type is set to SNM
  if(isset($form['form_type']) && $form['form_type']=='SNM'){
    //create settings on position 50 (right after Admin Label)
    if ( $position == 50 ) {
        ?>
        <li class="snm_input_setting field_setting">
            <label for="field_snm_value">
                <?php _e("SNM field name", "gravityforms"); ?>
                <?php
                  gform_tooltip("field_snm_value")
                ?>
            </label>
            <input type="text" id="field_snm_input" onchange="SetFieldProperty('snmInput', this.value);" class="fieldwidth-3" />
        </li>
        <?php

      }
  }
}

//Filter to add a new tooltip
add_filter( 'gform_tooltips', 'add_snm_tooltips' );
function add_snm_tooltips( $tooltips ) {
    $tooltips['field_snm_value'] = "<strong>Science Near Me field name</strong>Please refer to <a href=\"https://beta.sciencenearme.org/api/docs/v1.html#/default/opportunity_new\">https://beta.sciencenearme.org/api/docs/v1.html#/default/opportunity_new</a> for the SNM field names.";
    return $tooltips;
}

//now lets add the new SNM field to all field types and populate it
add_action( 'gform_editor_js', 'editor_script' );
function editor_script(){
	?>
	<script type="text/javascript">
		// Add our setting to these field types
		fieldSettings.text += ', .snm_input_setting';
		fieldSettings.textarea += ', .snm_input_setting';
		fieldSettings.email += ', .snm_input_setting';
		fieldSettings.phone += ', .snm_input_setting';
		fieldSettings.number += ', .snm_input_setting';
    fieldSettings.name += ', .snm_input_setting';
    fieldSettings.website += ', .snm_input_setting';
    fieldSettings.date += ', .snm_input_setting';
    fieldSettings.radio += ', .snm_input_setting';
    fieldSettings.checkbox += ', .snm_input_setting';
    fieldSettings.select += ', .snm_input_setting';
    fieldSettings.fileupload += ', .snm_input_setting';
    fieldSettings.address += ', .snm_input_setting';
    fieldSettings.list += ', .snm_input_setting';

		// Make sure our field gets populated with its saved value
		jQuery(document).on("gform_load_field_settings", function(event, field, form) {
	        	jQuery("#field_snm_input").val(field["snmInput"]);
	    	});
	</script>
	<?php
};

//add custom merge tag to calculate a formatted date
add_action( 'gform_after_submission', 'make_set_merge_tag', 10, 2 );
function make_set_merge_tag( $entry, $form ) {
  $merge_tag = '{formatted_date';
  foreach($entry as $key=>$text){
    if(strpos( $text, $merge_tag ) !== false){
      // the formatted_date merge tags needs 3 fields to work - date, time, timezone
      $formatted_date = '';
      $startPos         = strpos($text, '{formatted_date'); //pos of start of merge tag
      $closeBracketPos  = strpos($text, '}', $startPos); //find the closing bracket of the merge tag

      //pull full merge tag text
      $merge_text    = substr ( $text , $startPos, $closeBracketPos - $startPos + 1);

      //pull date field, if one isn't passed use current date
      $fieldID = pullMergeParam($merge_text, 'date');
      $date = ($fieldID !='' && isset($entry[$fieldID]) ? date("Y-m-d", strtotime($entry[$fieldID])) : date("Y-m-d"));

      //pull time field, if one isn't passed use current time
      $fieldID = pullMergeParam($merge_text, 'time');

      //if a time field is used, the value is returned as an array
      if($fieldID !='' && isset($entry[$fieldID]) && !empty($entry[$fieldID])){
        if(is_array($entry[$fieldID])){
          $time = $entry[$fieldID][0].':'.$entry[$fieldID][1].' '. $entry[$fieldID][2];
        }else{
          $time = $entry[$fieldID];
        }
      }else{
        $time = date("h:i a"); //current time
      }

      //pull timezone field, if not set use the wordpress timezone
      $fieldID = pullMergeParam($merge_text, 'timezone');
      $timeZone = ($fieldID != '' && isset($entry[$fieldID]) ? $entry[$fieldID] : wp_timezone_string());

      $now = new DateTime($date.' '.$time, new DateTimeZone($timeZone));
      $formatted_date = $now->format('c');

      //replace the merge tag with the formatted date
      $result  = GFAPI::update_entry_field( $entry['id'], $key, $formatted_date);
    }
  }
}

function pullMergeParam($merge_text, $param){
    $fieldStartPos  = strpos($merge_text, $param.'="');
    if ($fieldStartPos !== false) {
      $fieldStartPos += strlen($param.'="');   //add length to move past the parameter name

      //find the end of the field ID
      $fieldEndPos = strpos($merge_text, '"', $fieldStartPos);
      $fieldID     = substr($merge_text , $fieldStartPos, $fieldEndPos - $fieldStartPos);

      return $fieldID;
  }
  return ''; //parameter not found, return blank
}

add_filter('gform_column_input_content', 'add_list_field_classes',10,6);
function add_list_field_classes( $input, $input_info, $field, $text, $value, $form_id ) {
  if($field["type"]=='list' && $field["inputName"]=="date-time-list"){
    if(stripos($text, 'time')!== false){
      $tabindex = GFCommon::get_tabindex();
      $input_field_name = 'input_' . $field->id . '[]';
      $input_field_id = $field->id . "_" . str_replace(" ", "_", strtolower($text));

  	  if(!$value) { $value = "12 : 00 PM"; } // if we aren't seeing a set value, set it noon as default

      $input = '<input type="text" name="' . $input_field_name . '" value="'.$value.'" ' . $tabindex . ' class="time timepicker">';

    }elseif(stripos($text, 'date')!== false){
      //build field name, must match List field syntax to be processed correctly
      $input_field_name = 'input_' . $field->id . '[]';
      $input_field_id = 'input_' . $form_id . '_' . $field->id;
      $tabindex = GFCommon::get_tabindex();

      $input = '<input name="' . $input_field_name . '" id="' . $input_field_name . '" ' . $tabindex . ' type="date" placeholder="mm-dd-yyyy" onKeyDown="numbersAndDashes()" value="' . $value . '" class="datepicker medium mdy datepicker_no_icon hasDatepicker" aria-describedby="input_10_12_date_format">' .
              ' <span id="' . $input_field_id . '_date_format" class="screen-reader-text">Date Format: MM slash DD slash YYYY</span>';
    }
  }
  return $input;
}
add_filter( 'gform_column_input', 'set_column_input', 10, 5 );
function set_column_input( $input_info, $field, $column, $value, $form_id ) {
  if($field->inputName=='') {
    return $input_info;
  }

  if($field->inputName=='social-list' && $column=='Platform'){
    return array( 'type' => 'select', 'choices' => 'Facebook, Twitter, Instagram, YouTube, TikTok' );
  }else{
    return $input_info;
  }
}
