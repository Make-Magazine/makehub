<div class="uap-wrapper">

	<div class="col-right">

			<div class="uap-page-title"><?php _e('Ultimate Affiliate Pro - Filters & Hooks', 'uap');?></div>

		        <?php if ( $data ):?>
		            <table class="wp-list-table widefat fixed tags uap-admin-tables" >
										<thead>
				                <tr>
				                    <th class="manage-column"><?php _e('Name', 'uap');?></th>
						                <th class="manage-column" style="max-width: 10%;"><?php _e('Type', 'uap');?></th>
				                    <th class="manage-column"><?php _e('Description', 'uap');?></th>
				                    <th class="manage-column"><?php _e('File', 'uap');?></th>
				                </tr>
										</thead>
										<tbody>
				            <?php foreach ( $data as $hookName => $hookData ):?>
				                <tr>
				                    <td class="manage-column"><?php echo $hookName;?></td>
						                <td class="manage-column"><?php echo $hookData['type'];?></td>
				                    <td class="manage-column"><?php echo $hookData['description'];?></td>
				                    <td class="manage-column" style="font-size: 9px;">
																<?php if ( $hookData['file'] && is_array( $hookData['file'] ) ):?>
																		<?php foreach ( $hookData['file'] as $file ):?>
																				<div><?php echo $file;?></div>
																		<?php endforeach;?>
																<?php endif;?>
														</td>
				                </tr>
				            <?php endforeach;?>
										</tbody>
										<tfoot>
												<tr>
														<th class="manage-column"><?php _e('Name', 'uap');?></th>
														<th class="manage-column" style="max-width: 10%;"><?php _e('Type', 'uap');?></th>
														<th class="manage-column"><?php _e('Description', 'uap');?></th>
														<th class="manage-column"><?php _e('File', 'uap');?></th>
												</tr>
										</tfoot>
								</table>
		        <?php endif;?>

	</div>

</div>
