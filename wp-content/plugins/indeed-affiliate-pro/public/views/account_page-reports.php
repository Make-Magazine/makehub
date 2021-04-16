<div class="uap-ap-wrap">

	<?php if (!empty($data['title'])):?>
		<h3><?php echo $data['title'];?></h3>
	<?php endif;?>
	<?php if (!empty($data['message'])):?>
		<p><?php echo do_shortcode($data['message']);?></p>
	<?php endif;?>

<div class="uap-row">
<?php if (!empty($data['referrals'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-green">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo $data['referrals'];?></div><div class="uap-detail"><?php echo __('Total referrals', 'uap'); ?></div>
                <div class="uap-subnote"><?php echo __('rewards and commissions received by now', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
<?php if (!empty($data['total_paid'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-lightyellow">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo uap_format_price_and_currency($data['currency'], round($data['total_paid'], 2));?></div><div class="uap-detail"><?php echo __('Total Withdrawn Earnings (paid Referrals)', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
<?php if (!empty($data['total_unpaid'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-red">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo uap_format_price_and_currency($data['currency'], round($data['total_unpaid'], 2));?></div><div class="uap-detail"><?php echo __('Your current Balance', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
</div>
<div class="uap-row" style="margin-top:20px;">
<?php if (!empty($data['visits'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-lightgray">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo  $data['visits'];?></div><div class="uap-detail"><?php echo __('Total Clicks', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
<?php if (!empty($data['conversions'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box uap-account-box-lightblue">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo $data['conversions'];?></div><div class="uap-detail"><?php echo __('Converted Clicks', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
<?php if (!empty($data['success_rate'])): ?>
		<div class="uapcol-md-3">
			<div class="uap-account-no-box">
			  <div class="uap-account-no-box-inside">
				<div class="uap-count"><?php echo $data['success_rate'];?>%</div><div class="uap-detail"><?php echo __('Success Rate', 'uap'); ?></div>
			  </div>
			</div>
		</div>
<?php endif;?>
</div>



<?php
if (!empty($data['achivements'])): ?>
<div class="uap-profile-box-wrapper">
    	<div class="uap-profile-box-title"><span><?php echo __('Your Achievements', 'uap'); ?></span></div>
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-8">
                    <?php
						$k = 1;
                        foreach ($data['achivements'] as $item):
                        $current = (empty($item['current_rank'])) ? __('None', 'uap') : $item['current_rank'];
                        $prev = (empty($item['prev_rank'])) ? __('None', 'uap') : $item['prev_rank'];
                        ?>
                    	<div class="uap-achievements">
                    		<div class="uap-achievements-content">
                            <div class="uap-achievements-content-line">
							<span class=" uap-special-label">#<?php echo $k;?>.</span>
							<?php echo __('On', 'uap') . ' ' . uap_convert_date_to_us_format($item['add_date']) . ' ' . __('You moved from ', 'uap') . $prev . ' ' . __('to', 'uap') . ' ' . $current;?>.</div>
                            </div>
                     	</div>
                        <?php
						$k++;
                        endforeach;?>
                    
              </div>
          </div>
      </div>
</div>                     
<?php endif;?>

</div>
