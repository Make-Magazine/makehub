<?php
if (empty($ihc_error_register)){
		$ihc_error_register = array();
}
if (!class_exists('UserAddEdit')){
  require_once IHC_PATH . 'classes/UserAddEdit.class.php';
}
$obj_form = new \UserAddEdit();
$args = array(
        'user_id'              => $data['uid'],
        'type'                 => 'edit',
        'tos'                  => false,
        'captcha'              => false,
        'select_level'         => false,
        'action'               => '',
        'is_public'            => true,
        'register_template'    => $data['template'],
        'print_errors'         => $ihc_error_register,
      );
$obj_form->setVariable($args);
$form = $obj_form->form();
$form = apply_filters('ihc_update_profile_form_html', $form );

?>

<div class="iump-user-page-wrapper ihc_userpage_template_1">
  <div class="iump-user-page-box">
    <!--div class="iump-user-page-box-title"><?php esc_html_e('Update Profile', 'ihc');?></div-->
    <div class="iump-register-form <?php echo $data['template'];?>"><?php echo $form;?></div>
  </div>
</div>
