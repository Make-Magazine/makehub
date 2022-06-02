<?php
if(!function_exists('wpvc_overview_view')){
    function wpvc_overview_view(){
	?>
        <div id="wpvc-overview-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Overview view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_settings_view')){
    function wpvc_settings_view(){
	?>
        <div id="wpvc-settings-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Settings view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_category_view')){
    function wpvc_category_view(){
	?>
    <div id="wpvc-category-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Category view','voting-contest')."</h2>");
}



if(!function_exists('wpvc_voting_clear_voting_entry')){
    function wpvc_voting_clear_voting_entry(){
	?>
    <div id="wpvc-clear-voting-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Clear Voting Entries','voting-contest')."</h2>");
}


if(!function_exists('wpvc_custom_fields_view')){
    function wpvc_custom_fields_view(){
	?>
    <div id="wpvc-custom-field-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Custom fields','voting-contest')."</h2>");
}

if(!function_exists('wpvc_reg_custom_fields_view')){
    function wpvc_reg_custom_fields_view(){
	?>
    <div id="wpvc-register-custom-field-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Custom fields','voting-contest')."</h2>");
}

if(!function_exists('wpvc_voting_license')){
    function wpvc_voting_license($license,$status){
        
        if($_REQUEST['license'] == 'invalid')
            echo '<div id="message" class="error"><p>Invalid License key</p></div>';
        if($_REQUEST['license'] == 'valid')
            echo '<div id="message" class="updated notice notice-success is-dismissible"><p>License key validated</p></div>';
				
	?>
    <div id="wpvc-voting-license" data-url="<?php echo site_url();?>" data-key="<?php echo $license;?>"  data-status="<?php echo $status;?>">
    <?php
    apply_filters('wpvc_extension_licenses', '');
    ?>
    </div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting License','voting-contest')."</h2>");
}


if(!function_exists('wpvc_voting_move_contestants_view')){
    function wpvc_voting_move_contestants_view($cls,$msg){
        if($cls!='' && $msg!=''){
            echo '<div style="line-height:40px;" class="' . $cls . '">' . $msg . '</div>';
        }
	?>
    <div id="wpvc-voting-move-contestant" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Move Contestants','voting-contest')."</h2>");
}

if(!function_exists('wpvc_voting_import_contestants_view')){
    function wpvc_voting_import_contestants_view($cls,$msg){
        if($cls!='' && $msg!=''){
            echo '<div style="line-height:40px;" class="' . $cls . '">' . $msg . '</div>';
        }
	?>
    <div id="wpvc-voting-import-contestant" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Import Contestants','voting-contest')."</h2>");
}
if(!function_exists('wpvc_voting_export_contestants_view')){
    function wpvc_voting_export_contestants_view(){
	?>
    <div id="wpvc-voting-export-contestant" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Export Contestants','voting-contest')."</h2>");
}
if(!function_exists('wpvc_voting_vote_logs_view')){
    function wpvc_voting_vote_logs_view(){
	?>
    <div id="wpvc-voting-votes-logs" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Logs','voting-contest')."</h2>");
}


if(!function_exists('wpvc_voting_translations')){
    function wpvc_voting_translations(){
	?>
    <div id="wpvc-voting-translations" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Translations','voting-contest')."</h2>");
}


if(!function_exists('wpvc_migration_view')){
    function wpvc_migration_view(){
	?>
        <div id="wpvc-migration-page" data-url="<?php echo site_url();?>"></div>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting Migration view','voting-contest')."</h2>");
}

if(!function_exists('wpvc_useredit_view')){
    function wpvc_useredit_view($custom,$user_values){
	?>
        <h3 class="heading">Custom Fields</h3>
        <table class="form-table">
            <?php 
            if(!empty($custom)){
                foreach($custom as $cus){
                    $system_name = $cus['system_name'];
                    $value = $cus['valueArr'];
                ?>
                <tr>
                    <th><label for="contact"><?php echo $cus['question']; ?></label></th>
                    <td>
                        <?php if($cus['question_type']=='TEXT'){ ?>
                            <input type="text" class="input-text form-control" name="<?php echo $system_name; ?>" id="<?php echo $system_name; ?>" value="<?php echo $user_values[0][$system_name]; ?>" />
                        <?php } ?>
                        <?php if($cus['question_type']=='SINGLE'){ 
                                if(is_array($value)){
                                    foreach($value as $val){ ?>
                                        <input type="radio" id="<?php echo $system_name; ?>" name="<?php echo $system_name; ?>" value="<?php echo $val; ?>" <?php echo ($user_values[0][$system_name] == $val)?'checked':''; ?>>
                                        <label for="<?php echo $system_name; ?>"><?php echo $val; ?></label><br>
                                   <?php
                                    }
                                }
                            ?>
                        <?php } ?>
                        <?php if($cus['question_type']=='MULTIPLE'){ 
                                if(is_array($value)){
                                    foreach($value as $val){ ?>
                                    <input type="checkbox" id="<?php echo $system_name; ?>" name="<?php echo $system_name; ?>" value="<?php echo $val; ?>" <?php echo ($user_values[0][$system_name] == $val)?'checked':''; ?>>
                                    <label for="<?php echo $system_name; ?>"><?php echo $val; ?></label><br>
                                   <?php
                                    }
                                }
                            ?>
                        <?php } ?>
                        <?php if($cus['question_type']=='DROPDOWN'){ ?>
                            <select name="<?php echo $system_name; ?>">
                            <?php
                            if(is_array($value)){
                                    foreach($value as $val){ ?>
                                        <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                            <?php
                                    }
                                }
                            ?>
                            </select>
                        <?php } ?>
                        <?php if($cus['question_type']=='DATE'){ ?>
                            <input type="text" class="input-text form-control" name="<?php echo $system_name; ?>" id="<?php echo $system_name; ?>" value="<?php echo $user_values[0][$system_name]; ?>" />
                        <?php } ?>
                    </td>
                </tr>
                <?php
                }
            }
            ?>
            
        </table>
	<?php
    }
}else{
    die("<h2>".__('Failed to load Voting User Edit view','voting-contest')."</h2>");
}

?>


