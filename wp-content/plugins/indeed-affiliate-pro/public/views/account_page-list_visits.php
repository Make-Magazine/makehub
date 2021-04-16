<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
	<div class="uap-row">
		<div class="uapcol-md-3 uap-account-visits-tab1">
			<div class="uap-account-no-box uap-account-box-lightgray"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['visits'];?></div><div class="uap-detail"><?php _e('Total Numbers of Clicks', 'uap');?></div>
            <div class="uap-subnote"><?php echo __('How many times your affiliate link have been used', 'uap'); ?></div></div></div>
		</div>
		<div class="uapcol-md-3 uap-account-visits-tab2">
			<div class="uap-account-no-box uap-account-box-red"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['conversions'];?></div><div class="uap-detail"><?php _e('Conversions', 'uap');?></div>
                <div class="uap-subnote"><?php echo __('If customer successfully completes a certain action', 'uap'); ?></div></div></div>
		</div>
		<div class="uapcol-md-3 uap-account-visits-tab3">
			<div class="uap-account-no-box uap-account-box-lightblue"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['success_rate'] . ' %';?></div><div class="uap-detail"><?php _e('Success Rate', 'uap');?></div></div></div>
		</div>
	</div>
	<?php if ((!empty($data['items']) && is_array($data['items'])) || !empty($data['filtered'])):?>
    <div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php _e("Clicks History", 'uap');?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-12">
                <div class="uap-account-referrals-filter">
					<?php echo $data['filter'];?>
    			</div>
	<?php if (!empty($data['items']) && is_array($data['items'])): ?>
		<table class="uap-account-table">
			<thead>
				<tr>
					<th><?php _e("Landing Page", 'uap');?></th>
					<th><?php _e("Browser", 'uap');?></th>
					<th><?php _e("Device", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e("Converted", 'uap');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e("Landing Page", 'uap');?></th>
					<th><?php _e("Browser", 'uap');?></th>
					<th><?php _e("Device", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e("Converted", 'uap');?></th>
				</tr>
			</tfoot>
			<tbody class="uap-alternate">
			<?php foreach ($data['items'] as $array) : ?>
				<tr>
					<td><a href="<?php echo $array['url'];?>" target="_blank"><?php echo $array['url'];?></a></td>
					<td><?php echo $array['browser'];?></td>
					<td><?php echo $array['device'];?></td>
					<td><?php echo uap_convert_date_to_us_format($array['visit_date']);?></td>
					<td class="uap-special-label" style="text-align:center;"><?php
						if ($array['referral_id']) echo 'Yes';
						else echo 'No';
					?></td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<?php else: ?>
			 <div class="uap-account-detault-message">
						<div><?php _e('No Clicks found for your selection.', 'uap');?></div>
				</div>
		<?php endif;?>
        </div>
        </div>
        </div>
        </div>
			<?php else: ?>
				 <div class="uap-account-detault-message">
							<div><?php _e('Here you will see all your Clicks that will be received based on your activity. Start your Affiliate campaing to earn commission.', 'uap');?></div>
					</div>
	<?php endif;?>
</div>

<?php if (!empty($data['pagination'])):?>
	<?php echo $data['pagination'];?>
<?php endif;?>
