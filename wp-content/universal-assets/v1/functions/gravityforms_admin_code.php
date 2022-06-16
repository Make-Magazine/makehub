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
