<?php
$fieldName = 'ihc_user_custom_banner_src';
$fieldValue = isset( $data['userData']['ihc_user_custom_banner_src'] ) ? $data['userData']['ihc_user_custom_banner_src'] : '';
$rand = rand( 1, 10000000);
?>

<?php wp_enqueue_style( 'ihc-croppic_css', IHC_URL . 'assets/css/croppic.css' );?>
<?php wp_enqueue_script( 'ihc-jquery_mousewheel', IHC_URL . 'assets/js/jquery.mousewheel.min.js', [], null );?>
<?php wp_enqueue_script( 'ihc-croppic', IHC_URL . 'assets/js/croppic.js', array(), null );?>
<?php wp_enqueue_script( 'ihc-account_page-banner', IHC_URL . 'admin/assets/js/ihc_image_upload.js', [], null );?>

<script>
jQuery( document ).ready( function(){
		IhcImageUpload.init({
        triggerId					           : '<?php echo 'ihc_js_upload_image_' . $rand;?>',
        saveImageTarget		           : '<?php echo IHC_URL . 'public/ajax-upload.php';?>',
        cropImageTarget              : '<?php echo IHC_URL . 'public/ajax-upload.php';?>',
        imageSelectorWrapper         : '.ihc-upload-top-banner-wrapper',
        hiddenInputSelector          : '[name=<?php echo $fieldName;?>]',
        imageClass                   : 'ihc-image-photo',
        removeImageSelector          : '<?php echo '#ihc_upload_image_remove_bttn_' . $rand;?>',
		    buttonId 					           : 'ihc_top_custom_banner_js_bttn',
		    buttonLabel 			           : '<?php echo __('Upload', 'ihc');?>',
    })
})
</script>
<div class="iump-form-line">
	<h4><?php _e('Customer My Account Banner', 'ihc');?></h4>
	<p><?php _e('Customize customer Banner image or leave empty if you wish to be loaded the Plugin template default image.', 'ihc');?></p>


<div class="ihc-edit-banner-wrapper ihc-upload-image-wrapp">

    <div class="ihc-upload-top-banner-wrapper" >
        <?php if ( $fieldValue != '' ):?>
            <img src="<?php echo $fieldValue;?>" class="" />
        <?php endif;?>
    </div>
    <div class="ihc-clear"></div>

    <div class="ihc-content-left">
       <?php if ( $fieldValue == '' ){
         $upload = 'display:block;';
         $remove = 'display:none;';
       } else {
        $upload = 'display:none;';
        $remove = 'display:block;';
       } ?>
       <div class="ihc-upload-bttn-wrapp ihc-avatar-trigger" id="<?php echo 'ihc_js_upload_image_' . $rand;?>" style="<?php echo $upload;?>" >
           <div id="ihc_top_custom_banner_js_bttn" class="ihc-upload-avatar"><?php _e('Upload', 'ihc');?></div>
       </div>
       <span style="<?php echo $remove;?>" class="ihc-upload-image-remove-bttn" id="<?php echo 'ihc_upload_image_remove_bttn_' . $rand;?>"><?php _e('Remove', 'ihc');?></span>
    </div>

    <input type="hidden" value="<?php echo $fieldValue;?>" name="<?php echo $fieldName;?>" id="<?php echo 'ihc_upload_hidden_' . $rand;?>" data-new_user="<?php echo ( $data['uid'] == -1 ) ? 1 : 0;?>" />

</div>

</div>
