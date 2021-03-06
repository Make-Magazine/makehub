<?php wp_enqueue_script( 'ihc_register_modal', IHC_URL . 'assets/js/IhcRegisterModal.js', array(), false, false );?>

<?php if ( $content ):?>
    <div class="ihc-register-modal-trigger">
        <?php echo $content;?>
    </div>
<?php endif;?>

<div class="" id="ihc_register_modal" style="display: none;" data-title="<?php _e('Register', 'ihc');?>" >
    <?php echo do_shortcode( '[ihc-register-form-for-popup is_modal=true]' );?>
</div>

<?php
$preventDefault = empty($trigger) ? 0 : 1;
$triggerSelector = empty($trigger) ? '.ihc-register-modal-trigger' : '.' . $trigger;
?>
<script>
jQuery(document).ready(function(){
    IhcRegisterModal.init({
              triggerModalSelector  : '<?php echo $triggerSelector;?>',
              preventDefault        : <?php echo $preventDefault;?>
    })
})
</script>
