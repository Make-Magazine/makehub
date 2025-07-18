<?php
/**
 * BuddyPress Avatars crop template.
 *
 * This template is used to create the crop Backbone views.
 *
 * @since 2.3.0
 *
 * @package BuddyPress
 * @subpackage bp-attachments
 * @version 3.0.0
 */

?>
<script id="tmpl-bp-avatar-item" type="text/html">
	<div id="avatar-to-crop">
		<img class="skip-lazy" src="{{{data.url}}}"/>
	</div>
	<div class="avatar-crop-management">
		<div id="avatar-crop-pane" class="avatar" style="width:{{data.full_w}}px; height:{{data.full_h}}px">
			<img class="skip-lazy"  src="{{{data.url}}}" id="avatar-crop-preview"/>
		</div>
		<div id="avatar-crop-actions">
			<a class="button avatar-crop-submit" href="#"><?php esc_html_e( 'Save Image', 'buddypress' ); ?></a>
		</div>
	</div>
</script>
