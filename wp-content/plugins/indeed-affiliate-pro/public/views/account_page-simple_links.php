<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['content'])):?>
	<p><?php echo do_shortcode($data['content']);?></p>
<?php endif;?>

<form action="" method="post">
<div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php _e('Add New Referrer Link', 'uap');?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-10">
                <?php _e("Users will no longer avoid links that could benefit a certain affiliate, because they will not be able to know to which affiliate the link belongs to.", 'uap');?>
                </div>
            </div>
        </div>
        <div class="uap-profile-box-content">
            <div class="uap-row ">
            	<div class="uap-col-xs-6">
                    <div class="uap-ap-field">
                        <div class="uap-ap-field">
                            <div class="uap-account-title-label"><?php _e('Your Referrer Link', 'uap');?></div>
                            <input type="text" name="url" class="uap-public-form-control" />
                            <div class="uap-account-notes"><?php echo __("Submit one of your website page from where traffic should be tracked and recorded as coming from you.", 'uap').__(" You can submit up to <strong>", 'uap').$data['max_limit'].__(" links </strong>", 'uap');?></div>
                        </div>

                        <?php if (!empty($data['err'])):?>
                            <div class="uap-warning-box">
                                <div><?php echo $data['err'];?></div>
                            </div>
                        <?php endif;?>

                        <div class="uap-change-password-field-wrap">
                            <input type="submit" value="<?php _e('Add New', 'uap');?>" name="save" class="button button-primary button-large uap-js-submit-simple-link" />
                        </div>

              </div>
          </div>
       </div>
 </div>
</form>

<?php if (!empty($data['items'])):?>
<div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php _e('Your own Referrer Links', 'uap');?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-8">
                  <div class="uap-stuffbox">
                      <table class="uap-account-table">
                          <thead>
                              <tr>
                                  <th><?php _e('Referrer Link', 'uap');?></th>
                                  <th><?php _e('Status', 'uap');?></th>
                                  <th><?php _e('Remove', 'uap');?></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr>
                                  <th><?php _e('Referrer Link', 'uap');?></th>
                                  <th><?php _e('Status', 'uap');?></th>
                                  <th><?php _e('Remove', 'uap');?></th>
                              </tr>
                          </tfoot>
                          <tbody class="uap-alternate">
                              <?php foreach ($data['items'] as $item):?>
                              <tr>
                                  <td><a href="<?php echo $item['url'];?>" target="_blank"><?php echo $item['url'];?></a></td>
                                  <td>
                                      <?php if ($item['status']):?>
                                          <?php _e('Active', 'uap');?>
                                      <?php else:?>
                                          <?php _e('Pending', 'uap');?>
                                      <?php endif;?>
                                  </td>
                                  <td>
                                      <a href="<?php echo add_query_arg('del', $item['id'], $data['url']);?>" style="color: red;"><?php _e('Delete', 'uap');?></a>
                                  </td>
                              </tr>
                              <?php endforeach;?>
                          </tbody>
                      </table>
                  </div>
              </div>
           </div>
        </div>
      </div>
<?php endif;?>
</div>

<script>
jQuery('.uap-js-submit-simple-link').on( 'click', function(e){
		e.preventDefault();
		jQuery.ajax({
				type : "post",
				url : decodeURI(ajax_url),
				data : {
									 action						: "uap_ajax_save_simple_link",
									 url							: jQuery('[name=url]').val(),
									 currentUrl 			: '<?php echo $data['url'];?>',
							 },
				success: function (response) {
						if (response){
							document.location.href= response;
							return;
						}
						location.reload();
				}
	 });
})
</script>
