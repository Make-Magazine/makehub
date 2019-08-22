<style>

/*.uap-avatar-trigger .cropControls {
    position: relative;
    background-color: rgba(0,0,0,0.35) !important;
    height: 48px !important;
    bottom: 0px !important;
    right: 0px !important;
}*/
.uap-avatar-trigger .cropControls .cropControlUpload{

}
.uap-avatar-trigger .cropControls .cropControlUpload:before{

}
.uap-user-avatar-wrapp{
    width: 150px;
    height: 150px;
}

</style>
<?php wp_enqueue_style( 'uap-croppic_css', UAP_URL . 'assets/css/croppic.css' );?>
<?php wp_enqueue_script( 'uap-jquery_mousewheel', UAP_URL . 'assets/js/jquery.mousewheel.min.js', array(), null );?>
<?php wp_enqueue_script( 'uap-croppic', UAP_URL . 'assets/js/croppic.js', array(), null );?>
<?php wp_enqueue_script( 'uap-image_croppic', UAP_URL . 'assets/js/image_croppic.js', array(), null );?>

<script>
jQuery( document ).ready( function(){
    UapAvatarCroppic.init({
        triggerId					           : '<?php echo 'js_uap_trigger_avatar' . $data['rand'];?>',
        saveImageTarget		           : '<?php echo UAP_URL . 'public/ajax-upload.php';?>',
        cropImageTarget              : '<?php echo UAP_URL . 'public/ajax-upload.php';?>',
        imageSelectorWrapper         : '.uap-upload-image-wrapp',
        hiddenInputSelector          : '[name=<?php echo $data['name'];?>]',
        imageClass                   : 'uap-member-photo',
        removeImageSelector          : '<?php echo '#uap_upload_image_remove_bttn_' . $data['rand'];?>',
		    buttonId 					           : 'uap-avatar-button',
		    buttonLabel 			           : '<?php echo __('Upload', 'uap');?>',
    });
});
</script>


<div class="">

    <div class="uap-upload-image-wrapp" >
        <?php if ( !empty($data['imageUrl']) ):?>
            <img src="<?php echo $data['imageUrl'];?>" class="<?php echo $data['imageClass'];?>" />
        <?php else:?>
            <?php if ( $data['name']=='uap_avatar' ):?>
                <div class="uap-no-avatar uap-member-photo"></div>
            <?php endif;?>
        <?php endif;?>
        <div class="uap-clear"></div>
    </div>
    <div class="uap-content-left">
    	<div class="uap-avatar-trigger" id="<?php echo 'js_uap_trigger_avatar' . $data['rand'];?>" >
        	<div id="uap-avatar-button" class="uap-upload-avatar"><?php _e('Upload', 'uap');?></div>
        </div>
        <span style ="visibility: hidden;" class="uap-upload-image-remove-bttn" id="<?php echo 'uap_upload_image_remove_bttn_' . $data['rand'];?>"><?php _e('Remove', 'uap');?></span>
    </div>
    <input type="hidden" value="<?php echo $data['value'];?>" name="<?php echo $data['name'];?>" id="<?php echo 'uap_upload_hidden_' . $data['rand'];?>" data-new_user="<?php echo ( $data['user_id'] == -1 ) ? 1 : 0;?>" />

    <?php if (!empty($data['sublabel'])):?>
        <label class="uap-form-sublabel"><?php echo uap_correct_text($data['sublabel']);?></label>
    <?php endif;?>
</div>
