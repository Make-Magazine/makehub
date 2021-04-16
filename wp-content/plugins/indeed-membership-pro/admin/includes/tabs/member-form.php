<form action="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>" method="post" class="ihc-form-create-edit" enctype="multipart/form-data" >
    <?php if ( $data['fields'] ) : ?>
        <?php foreach ( $data['fields'] as $field ): ?>
            <?php $fieldName = isset( $field['name'] ) ? $field['name'] : '';?>
            <?php $fieldValue = isset( $data['userData'][$fieldName] ) ? $data['userData'][$fieldName] : '';?>
            <?php
            if ( empty( $field['other_args'] ) ){
                $field['other_args'] = '';
            }
            if ( empty( $field['disabled'] ) ){
                $field['disabled'] = '';
            }
            if ( empty( $field['id'] ) ){
                $field['id'] = '';
            }
            if ( empty( $field['class'] ) ){
                $field['class'] = '';
            }
            if ( empty( $field['placeholder'] ) ){
                $field['placeholder'] = '';
            }
            ?>
            <?php switch( $field['type'] ):
                    case 'text':
                    case 'unique_value_text':
                    case 'conditional_text':?>
                      <div class="iump-form-line-register iump-form-text">
                          <label class="iump-labels-register"><?php echo $field['label'];?></label>
                          <input class="ihc-form-element-text" type="text" name="<?php echo $fieldName;?>" value="<?php echo $fieldValue;?>" />
                      </div>
                      <?php break;?>

                    <?php case 'number':?>
                      <div class="iump-form-line-register iump-form-number">
                          <label class="iump-labels-register"><?php echo $field['label'];?></label>
                          <input class="ihc-form-element-number" type="number" name="<?php echo $fieldName;?>" value="<?php echo $fieldValue;?>" />
                      </div>
                      <?php break;?>

                    <?php case 'textarea':?>
                      <div class="iump-form-line-register iump-form-textarea">
                          <label class="iump-labels-register"><?php echo $field['label'];?></label>
                          <textarea class="iump-form-textarea ihc-form-element-textarea" name="<?php echo $fieldName;?>"><?php echo $fieldValue;?></textarea>
                      </div>
                      <?php break;?>

                    <?php case 'password':?>
                      <div class="iump-form-line-register iump-form-password">
                          <label class="iump-labels-register"><?php echo $field['label'];?></label>
                          <input class="ihc-form-element-password" type="password" name="<?php echo $fieldName;?>" value="<?php echo $fieldValue;?>" />
                        </div>
                        <?php break;?>

                       <?php case 'single_checkbox':?>
                              <?php $checked = empty($fieldValue) ? '' : 'checked';?>
                              <div class="iump-form-line-register iump-form-single_checkbox">
                              <div class="ihc-tos-wrap" id="<?php echo  $field['id'];?>">
                				          <input type="checkbox" value="1" name="<?php echo $fieldName;?>" class="<?php echo $field['class'];?>" <?php echo $checked;?>  />
                								  <?php echo @$field['label'];?>
                              </div>
                            </div>
                          <?php break;?>

                          <?php case 'checkbox':?>
                    					<?php if ( empty ( $field['values'] ) ) break;?>
                              <div class="iump-form-line-register iump-form-checkbox">
                                <label class="iump-labels-register"><?php echo $field['label'];?></label>
                              <div class="iump-form-checkbox-wrapper" id="<?php echo 'ihc_checkbox_parent_' . rand(1,1000);?>">
                                <?php foreach ($field['values'] as $v):
                      						if (is_array( $fieldValue ) ){
                      							$checked = ( in_array( $v, $fieldValue ) ) ? 'checked' : '';
                      						} else {
                      							$checked = ( $v == $fieldValue ) ? 'checked' : '';
                      						}
                      						$id_field = (isset($field['id']) && $field['id'] != "" ) ? 'id="'.$field['id'].'"' : '';
                                  ?>
                                  <div class="iump-form-checkbox">
                          						<input type="checkbox" name="<?php echo $fieldName;?>[]" <?php echo $id_field;?> class="<?php echo $field['class'];?>" <?php echo $checked;?> value="<?php echo ihc_correct_text($v, false, true );?>" <?php echo  $field['other_args'];?> <?php echo $field['disabled'];?>  />
                          						<?php echo ihc_correct_text($v);?>
                      						</div>
                      					<?php endforeach;?>
                              </div>
                            </div>
                              <?php	break;?>

                            <?php case 'radio':?>
                      					<?php if ( empty ( $field['values'] ) ) break;?>
                                <div class="iump-form-line-register iump-form-radio">
                                  <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                <div class="iump-form-checkbox-wrapper" id="<?php echo 'ihc_checkbox_parent_' . rand(1,1000);?>">
                                <?php foreach ($field['values'] as $v):
                      							$checked = ($v==$fieldValue) ? 'checked' : '';
                      						   $id_field = (isset($field['id']) && $field['id'] != "" ) ? 'id="'.$field['id'].'"' : '';
                                  ?>
                                  <div class="iump-form-checkbox">
                          						<input type="radio" name="<?php echo $fieldName;?>" <?php echo $id_field;?> class="<?php echo $field['class'];?>" <?php echo $checked;?> value="<?php echo ihc_correct_text($v, false, true );?>" <?php echo  $field['other_args'];?> <?php echo $field['disabled'];?>  />
                          						<?php echo ihc_correct_text($v);?>
                      						</div>
                      					<?php endforeach;?>
                                </div>
                              </div>
                              <?php break;?>

                            <?php case 'select':?>
                                <?php if ( empty ( $field['values'] ) ) break;?>
                                <div class="iump-form-line-register iump-form-select">
                                    <?php $id_field = (isset($field['id']) && $field['id'] != "" ) ? 'id="'.$field['id'].'"' : '';?>
                                    <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                    <select name="<?php echo $fieldName;?>" <?php echo $id_field;?> class="iump-form-select ihc-form-element-select<?php echo $field['class'];?>" <?php echo $field['other_args'];?> <?php echo $field['disabled'];?> >
                                        <?php foreach ( $field['values'] as $k=>$v ):?>
                                            <?php $selected = ($k==$fieldValue) ? 'selected' : '';?>
                                            <option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo ihc_correct_text( $v, false, true );?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <?php break;?>

                            <?php case 'multi_select':?>
                                <?php if ( empty ( $field['values'] ) ) break;?>
                                <div class="iump-form-line-register iump-form-multi_select">
                                  <?php $id_field = (isset($field['id']) && $field['id'] != "" ) ? 'id="'.$field['id'].'"' : '';?>
                                      <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                      <select name="<?php echo $fieldName;?>[]" <?php echo $id_field;?> class="iump-form-multiselect ihc-form-element-multi_select <?php echo $field['class'];?>" <?php echo $field['other_args'];?> <?php echo $field['disabled'];?> multiple >
                                          <?php foreach ( $field['values'] as $k=>$v ):?>
                                              <?php
                                                if (is_array($fieldValue)){
                                    							$selected = (in_array($v, $fieldValue)) ? 'selected' : '';
                                    						} else {
                                    							$selected = ($v==$fieldValue) ? 'selected' : '';
                                    						}
                                              ?>
                                              <option value="<?php echo $v;?>" <?php echo $selected;?> ><?php echo ihc_correct_text( $v, false, true );?></option>
                                          <?php endforeach;?>
                                      </select>
                                    </div>
                                  <?php break;?>

                              <?php case 'ihc_country':?>
                                  <div class="iump-form-line-register iump-form-ihc_country iump-form-ihc_country-wrapper"><?php
                            				wp_enqueue_style( 'ihc_select2_style' );
                            				wp_enqueue_script( 'ihc-select2' );

                            				if (empty($field['id'])){
                            					$field['id'] = $field['name'] . '_field';
                            				}
                            				$countries = ihc_get_countries();
                            				$update_cart = 'ihcUpdateCart();';
                            				if (isset($field['form_type']) && $field['form_type']=='edit'){
                            					$update_cart = '';
                            				}

                            				$onchange = 'onChange="ihcUpdateStateField();';

                            				if ( empty( $fieldValue ) ){
                            						$fieldValue = ihcGetDefaultCountry();
                            				}
                                    ?>
                                    <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                    <select name="<?php echo $fieldName;?>" id="<?php echo  $field['id'];?>" <?php echo $onchange;?> <?php echo $update_cart;?> >
                                    <?php	foreach ($countries as $k=>$v): ?>
                            					<?php $selected = ($fieldValue==$k) ? 'selected' : '';?>
                                      <option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
                            				<?php endforeach;?>

                                    </select>
                                    <ul id="ihc_countries_list_ul" style="display: none;"></ul>
                            				<?php if ( empty( $field['is_modal'] ) ):?>
                            					<script>
                            							jQuery(document).ready(function(){
                            									jQuery("#<?php echo $field['id'];?>").select2({
                            										placeholder: "Select Your Country",
                            										allowClear: true,
                            										selectionCssClass: "ihc-select2-dropdown"
                            									});
                            							});
                            					</script>
                            				<?php endif;?>
                                    </div>
                            				<?php break;?>

                            		<?php case 'ihc_state':?>
                                    <div class="iump-form-line-register iump-form-ihc_state iump-form-ihc_state-wrapper">
                                        <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                				<input class="ihc-form-element-ihc_state" type="text" onBlur="ihcUpdateCart();" name="<?php echo $field['name'];?>"
                                            id="<?php echo isset( $field['id'] ) ? $field['id'] : '';?>" class="<?php echo isset( $field['class'] ) ? $field['class'] : '';?>"
                                            value="<?php echo isset( $fieldValue ) ? ihc_correct_text( $fieldValue ) : '';?>" placeholder="<?php echo isset( $field['placeholder'] ) ? $field['placeholder'] : '';?>"
                                            <?php echo isset( $field['other_args'] ) ? $field['other_args'] : '';?> <?php echo isset( $field['disabled'] ) ? $field['disabled'] : '';?>
                                        />
                                    </div>
                            				<?php break;?>

                                <?php case 'date':?>
                                <div class="iump-form-line-register iump-form-date">
                                  <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                    <?php
                              				wp_enqueue_script('jquery-ui-datepicker');
                              				if (empty($field['class'])){
                              					$field['class'] = 'ihc-date-field';
                              				}

                              				global $ihc_jquery_ui_min_css;
                              				if (empty($ihc_jquery_ui_min_css)){
                              					$ihc_jquery_ui_min_css = true;
                                        ?>
                                        <link rel="stylesheet" type="text/css" href="<?php echo IHC_URL . 'admin/assets/css/jquery-ui.min.css';?>"/>
                                        <?php
                              				}

                              				if (empty($field['callback'])){
                              					$field['callback'] = '';
                              				}

                                      ?>

                                      <input type="text" value="<?php echo $fieldValue;?>" name="<?php echo $fieldName;?>" id="<?php echo $field['id'];?>" class="iump-form-datepicker <?php echo $field['class'];?>" <?php echo $field['other_args'];?> <?php echo $field['disabled'];?> placeholder="<?php echo $field['placeholder'];?>" />
                              				<script>
                              					jQuery(document).ready(function() {
                              						var currentYear = new Date().getFullYear() + 10;
                              						jQuery(".<?php echo $field['class'];?>").datepicker({
                              							dateFormat   : "dd-mm-yy",
                              							changeMonth  : true,
                              						  changeYear   : true,
                              							yearRange    : "1900:"+currentYear,
                              							onClose      : function(r) {
                              								<?php echo $field['callback'];?>
                              							}
                              					});
                              				});
                              				</script>
                                  </div>
                              		<?php	break;?>

                              <?php case 'upload_image':?>
                                  <div class="iump-form-line-register iump-form-upload_image">
                                  <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                  <div class="ihc-upload-image-wrapper">
                                    <?php
                              					$attr = $field;
                              					$attr['rand'] = rand(1, 10000);
                              					$attr['imageClass'] = 'ihc-member-photo';
                                        $attr['value'] = $fieldValue;
                              					if (empty($data['user_id'])){
                              					 		$attr['user_id'] = -1;
                              					}
                              					$attr['imageUrl'] = '';
                              					if ( !empty($attr['value']) ){
                              							if (strpos($attr['value'], "http")===0){
                              									$attr['imageUrl'] = $attr['value'];
                              							} else {
                              									$tempData = \Ihc_Db::getMediaBaseImage($attr['value']);
                              									if (!empty($tempData)){
                              										$attr['imageUrl'] = $tempData;
                              									}
                              							}
                              					}
                              					$viewObject = new \Indeed\Ihc\IndeedView();
                              					echo $viewObject->setTemplate( IHC_PATH . 'admin/includes/tabs/upload_image.php' )->setContentData( $attr )->getOutput();
                                      ?>
                                    </div>
                                  </div>
                            			<?php break;?>

                                  <?php case 'file':?>
                                      <?php
                                          include IHC_PATH . 'admin/includes/tabs/upload_file.php';
                                      ?>
                            				<?php break;?>
                              <?php case 'plain_text':
                              		echo ihc_correct_text( $field['plain_text_value'] );
                                break;?>

            <?php endswitch;// end of switch ?>
            <?php if ( !empty( $attr['sublabel'] ) ):?>
                <label class="iump-form-sublabel"><?php echo ihc_correct_text( $attr['sublabel'] );?></label>
            <?php endif;?>
        <?php endforeach;?>
    <?php endif;?>
    <?php if ( $data['uid'] ) :?>
        <input type="hidden" name="ID" value="<?php echo $data['uid'];?>" />
    <?php endif;?>

    <?php
    // Select Level
    $level_select_options[-1] = '...';
    foreach ( $data['subscriptions'] as $k=>$v ){
        $level_select_options[$k] = $v['label'];
    }
    $attributes = array(
        'args'							=> array(
                              'type' 						=> 'select',
                              'multiple_values' => $level_select_options,
                              'value' 					=> -1,
                              'name' 						=> 'ihc_user_levels',
        ),
        'user_levels' 			=> $data['userSubscriptions'],
        'userLevelsArray'		=> ( $data['userSubscriptions'] && $data['userSubscriptions']>-1 ) ? explode( ',', $data['userSubscriptions'] ) : array(),
        'uid'								=> $data['uid'],
    );
    $view = new \Indeed\Ihc\IndeedView();
    echo $view->setTemplate( IHC_PATH . 'admin/includes/tabs/register-select_level.php')
              ->setContentData( $attributes, true )
              ->getOutput();
    ?>

    <?php
    // user subscriptions expire table
    if ( $data['uid'] ):
      $view = new \Indeed\Ihc\IndeedView();
      $attr = [
        'user_levels'			=> explode( ',', $data['userSubscriptions'] ),
        'uid'							=> $data['uid'],
      ];
      echo $view->setTemplate(IHC_PATH . 'admin/includes/tabs/edit_user-levels_expire_table.php')->setContentData($attr, true)->getOutput();
    endif;
    ?>

    <div class="ihc-admin-edit-user-additional-settings-wrapper">
        <?php // Wp Role ?>
        <h2><?php _e('Additional Settings','ihc');?></h2>
        <p><?php _e('Extra settings available for Administration purpose','ihc');?></p>



            <?php
                include IHC_PATH . 'admin/includes/tabs/custom_banner.php';
            ?>


        <?php
            include IHC_PATH . 'admin/includes/tabs/register-wp_role.php';
        ?>

        <?php
        // Overview Post Select
        $default_pages_arr = ihc_return_meta_arr('general-defaults');
        $default_pages_arr = array_diff_key($default_pages_arr, array(
                                        'ihc_general_redirect_default_page'			=> '',
                                        'ihc_general_logout_redirect'						=> '',
                                        'ihc_general_register_redirect'					=> '',
                                        'ihc_general_login_redirect'						=> ''
                            )
        );//let's exclude the redirect pages
        $args = array(
            'posts_per_page'   => 100,
            'offset'           => 0,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => array( 'post', 'page' ),
            'post_status'      => 'publish',
            'post__not_in'	   => $default_pages_arr,
        );

        $posts_array = get_posts( $args );
        $arr['-1'] = '...';
        foreach ($posts_array as $k=>$v){
          $arr[$v->ID] = $v->post_title;
        }
        ?>

        <div class="iump-form-line">
            <h4><?php _e('Custom Dashboard message', 'ihc');?></h4>
            <p><?php _e('The Dashboard section from the Account Page can display general content or specific content for each member. You may choose a specific post content for this member or leave the default setup.', 'ihc');?></p>
            <select name="ihc_overview_post" class='ihc-form-element ihc-form-element-select ihc-form-select '>
                <?php foreach ( $arr as $key=>$value ):?>
                    <option value="<?php echo $key;?>" <?php echo ( $key == $data['ihc_overview_post'] ) ? 'selected' : '' ;?> ><?php echo $value;?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="iump-submit-form">
            <input type="submit" name="ihc_save_member" id="ihc_submit_bttn" class="button button-primary button-large" value="<?php _e( 'Save Changes', 'ihc');?>" />
        </div>

</form>
