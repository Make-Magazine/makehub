<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['stats'])):?>
<div class="uap-row">
		<div class="uapcol-md-4 uap-account-overview-tab1">
			<div class="uap-account-no-box uap-account-box-green" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['referrals']; ?> </div>
				<div class="uap-detail"><?php echo __('Total Referrals', 'uap'); ?></div>
                <div class="uap-subnote"><?php echo __('rewards and commissions received by now', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab2">
			<div class="uap-account-no-box uap-account-box-lightyellow" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['paid_referrals_count']; ?> </div>
				<div class="uap-detail"><?php echo __('Paid Referrals', 'uap'); ?></div>
                <div class="uap-subnote"><?php echo __('withdrawn number of referrals until now', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab3">
			<div class="uap-account-no-box uap-account-box-red" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['unpaid_referrals_count']; ?> </div>
				<div class="uap-detail"><?php echo __('UnPaid Referrals', 'uap'); ?></div>
                <div class="uap-subnote"><?php echo __('which have been not withdrawn yet', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab4">
			<div class="uap-account-no-box uap-account-box-lightblue " style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['payments']; ?> </div>
				<div class="uap-detail"><?php echo __('Total Payout Transactions', 'uap'); ?></div>
			 </div>
			</div>
		</div>
</div>
<div class="uap-row">
	<div class="uapcol-md-2 uap-account-overview-tab5">
			<div class="uap-account-no-box uap-account-box-lightgray">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['paid_payments_value'], 2) );?> </div>
				<div class="uap-detail"><?php echo __('Withdrawn  Earnings by Now (total Transactions)', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-2 uap-account-overview-tab6">
			<div class="uap-account-no-box uap-account-box-blue">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['unpaid_payments_value'], 2));?> </div>
				<div class="uap-detail"><?php echo __('Current Account Balance', 'uap'); ?></div>
			 </div>
			</div>
		</div>
</div>
<div class="uap-profile-box-wrapper">
        <div class="uap-profile-box-content">
        	<div class="uap-row ">
            	<div class="uap-col-xs-12">
   						 <div class="uap-account-help-link">
			  				<?php _e('You can learn more about Affiliate program and to start earning referrals ', 'uap');?>
              					<a href="<?php echo $data['help_url'];?>">
			  						<?php _e('here', 'uap');?>
              					</a>
                    		</div>
        		</div>
        	</div>
        </div>
     </div>
     <div class="uap-profile-box-wrapper uap-account-summary-wrapper" style="margin:0;">
        <div class="uap-profile-box-content"  style="padding:0;">
        	<div class="uap-row ">
            	<div class="uap-col-xs-8">
   					<div class="uap-account-summary-month uap-account-summary-graph-warpper">
                    	<div class="uap-account-summary-month-header">
                        	<div class="uap-account-summary-graph-title">
                            	<?php _e('Earnings Overview', 'uap');?> <span><?php _e('(for Last 30 days)', 'uap');?></span>

                            </div>
                        </div>
                    	 <div class="uap-account-summary-month-content">
                            <div class="uap-account-summary-graph-content">
                            		<?php _e('Line Graph for Earnings back to 30 days.', 'uap');?>
																<?php if ( !empty( $data['statsForLast30'] ) ):?>
																	 <div class="col-4" ><canvas id="chart-1" style="min-height: 250px;" ></canvas></div>
																<?php endif;?>
                            </div>
                            <div class="uap-account-summary-summary-content">
                            	<div class="uap-row">
                                	<div class="uap-col-xs-6 uap-account-summary-summary-content-first-col">
                                    	<div class="uap-account-summary-summary-data-title"><?php _e('Total Earnings', 'uap');?></div>
                                        <div class="uap-account-summary-summary-data-content"><?php echo uap_format_price_and_currency($data['stats']['currency'], $data['referralsExtraStats']['total_earnings'] );?></div>
                                    </div>

                                	<div class="uap-col-xs-6">
                                    	<div class="uap-account-summary-summary-data-title"><?php _e('Clicks', 'uap');?></div>
                                        <div class="uap-account-summary-summary-data-content"><?php echo $data['referralsExtraStats']['visits'];?></div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
        		</div>
                <div class="uap-col-xs-4">
                	<div class="uap-account-summary-month">
                    	<div class="uap-account-summary-month-header">
                        	<div class="uap-account-summary-month-title">
                            	<?php _e('Summary for This Month', 'uap');?>
                            </div>
                        </div>
                        <div class="uap-account-summary-month-content">
                        	<div class="uap-account-summary-month-data">
                            	<div class="uap-account-summary-month-data-row uap-row uap-account-summary-month-data-row-first">
                                	<div class="uap-col-xs-7"><?php _e('Total Referrals:', 'uap');?></div><div class="uap-col-xs-5"><?php echo $data['referralsExtraStats']['total_referrals'];?></div>
                                </div>
                                <div class="uap-account-summary-month-data-row uap-row">
                                    <div class="uap-col-xs-7"><?php _e('Total Earnings:', 'uap');?></div>
																		<div class="uap-col-xs-5">
																			<?php echo uap_format_price_and_currency($data['stats']['currency'], $data['referralsExtraStats']['total_earnings']);?>
																		</div>
                                </div>
                                <div class="uap-account-summary-month-data-row uap-row">
                                    <div class="uap-col-xs-7"><?php _e('UnVerified Referrals:', 'uap');?></div><div class="uap-col-xs-5"><?php echo $data['referralsExtraStats']['unverified_referrals'];?></div>
                                </div>
                                <div class="uap-account-summary-month-data-row uap-row">
                                    <div class="uap-col-xs-7"><?php _e('Clicks:', 'uap');?></div><div class="uap-col-xs-5"><?php echo $data['referralsExtraStats']['visits'];?></div>
                                </div>
                                <div class="uap-account-summary-month-data-row uap-row">
                                    <div class="uap-col-xs-7"><?php _e('Conversion:', 'uap');?></div><div class="uap-col-xs-5"><?php echo $data['referralsStats']['success_rate'];?>%</div>
                                </div>
                        	</div>
                        </div>

                    </div>
                </div>
        	</div>
        </div>
     </div>
	<!--div class="uap-public-general-stats">
		<div><?php echo __('Total number of Referrals:') . $data['stats']['referrals'];?></div>
		<div><?php echo __('Total number of Payments:') . $data['stats']['payments'];?></div>
		<div><?php echo __('Total number of Paid Referrals:') . $data['stats']['paid_referrals_count'];?></div>
		<div><?php echo __('Total number of UnPaid Referrals:') . $data['stats']['unpaid_referrals_count'];?></div>
		<div><?php echo __('Total value of Paid Payments:') . uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['paid_payments_value'], 2));?></div>
		<div><?php echo __('Total value of Unpaid Payments:') . uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['unpaid_payments_value'], 2));?></div>
	</div-->
<?php endif;?>

<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
</div>

<?php if ( !empty( $data['statsForLast30'] ) ):?>
<?php wp_enqueue_script( 'uap-moment.js', UAP_URL . 'assets/js/moment.min.js', [], false );?>
<?php wp_enqueue_script( 'uap-chart.js', UAP_URL . 'assets/js/chart.min.js', [], false );?>

<script>

	var uapDates = [<?php foreach( $data['statsForLast30'] as $date => $amounts ) { $date = uap_convert_date_to_us_format($date);echo "'$date', "; }?>];
	var uapAmounts = [<?php foreach( $data['statsForLast30'] as $date => $amounts ) { $amount = uap_format_price_and_currency($data['stats']['currency'], $amounts);echo "'$amount', "; }?>];

jQuery(document).ready(function(){

  var options = {
    maintainAspectRatio: false,
    spanGaps: false,
    elements: {
      line: {
        tension: 0.01
      }
    },
    plugins: {
      filler: {
        propagate: false
      }
    },
    scales: {
      x: {
        ticks: {
          autoSkip: false,
          maxRotation: 0,
        },
      },
      xAxes: [ { gridLines: { display: false } } ],
      yAxes: [{
             ticks: {
                 beginAtZero: true,
                 userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }

                 },
             },
						scaleLabel: {
							display: true,
							labelString: '<?php echo __( 'Earnings received', 'uap' ) . ' ('.$data['stats']['currency'].')';?>'
						}
      }],
      scaleShowVerticalLines: false,
    },
		animation: {
					duration		: 2000,
					easing			: 'easeInQuad',
				},
  }; // end of options

  var uapChart = new Chart('chart-1', {
    type    : 'line',
    data    : {
      labels    : [<?php
                      $i=1;
                      foreach( $data['statsForLast30'] as $date => $referralNumber ) {
                          if ( $i%2==0 )continue;
                          $temporaryDate = explode( '-', $date);
                          $day = isset($temporaryDate[2]) ? $temporaryDate[2] : $date;
                          echo "'$day'" . ",";
                      }?>],
      datasets  : [{
	        backgroundColor   : 'rgba(201,218,232,0.5)',
	        borderColor       : '#c9dae8',
	        data              : [<?php foreach( $data['statsForLast30'] as $date => $amount ) echo $amount . ",";?>], //referrals counts
	        label             : '<?php _e('Earnings', 'uap');?>',
					pointRadius				: 3,
					fill							: 'start',
					borderWidth				: 2
      }]
    },
    options : Chart.helpers.merge(options, {
              legend: {
                    display: false
              },
              title: {
                text    : '',
                display : true
              },
							tooltips: {
										intersect		: false,
										position		: 'nearest',
										callbacks: {
											label: function(tooltipItem, data) {
													string = '';
													window.uapAmounts.forEach(function( val, i ){
															if ( i == tooltipItem.index ){
																string = val;
															}
													});
													return string;
			                },
											title: function(tooltipItem, data) {
												string = '';
												window.uapDates.forEach(function( val, i ){
														if ( i == tooltipItem[0].index ){
															string = val;
														}
												});
												return string;
			                },
										}
								}
    }),

  });

});
</script>

<?php endif;?>
