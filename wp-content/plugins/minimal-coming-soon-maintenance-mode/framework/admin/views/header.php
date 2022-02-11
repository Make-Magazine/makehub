<div class="csmm-cnt-fix">
	<div class="csmm-fix-wp38">
		<div class="csmm-header csmm-clearfix">
            <?php
            if(csmm_get_rebranding() !== false){
                echo '<img src="' . csmm_get_rebranding('logo_url') . '" class="csmm-logo" style="max-width:none;max-height:50px;" />';
            } else {
                echo '<img src="' . CSMM_URL . '/framework/admin/img/mm-icon-dark.png" class="csmm-logo" />';
            }
            ?>
			
			<p>
            <?php
            if(csmm_get_rebranding() !== false){
                echo '<strong>' . csmm_get_rebranding('name') . '</strong>';
            } else {
                echo '<strong>Coming Soon &amp; Maintenance Mode </strong><strong style="color: #fe2929;">PRO</strong>';
            }
            
            if(csmm_get_rebranding() !== false){
                $plugin_by = '<span>by <a href="' . csmm_get_rebranding('url') . '" target="_blank"> ' . csmm_get_rebranding('company_name') . '</a></span>';
            } else {
                $plugin_by = '<span>by <a href="https://www.webfactoryltd.com/" target="_blank">WebFactory Ltd</a></span>';
            }

            if(csmm_whitelabel_filter()){
                echo $plugin_by;
            }
            ?>
			</p>

      <?php
      global $csmm_lc;
      if ($csmm_lc->is_active()) {
      ?>
        <div id="header-right">
        <div id="header-status" title="Click to change Coming Soon status">
          <label for="">Coming Soon Mode Status:</label> <div class="csmm-status-wrapper <?php echo ($signals_csmm_options['status']== '0')? 'off': 'on'; ?>"><span class="csmm-status-btn csmm-status-off">OFF</span><span class="csmm-status-btn csmm-status-on">ON</span></div>
        </div>
        </div>
      <?php
      }
      ?>
        </div><!-- .csmm-header -->
