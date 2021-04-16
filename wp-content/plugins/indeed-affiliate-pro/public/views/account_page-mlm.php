<div class="uap-ap-wrap">

	<?php if (!empty($data['title'])):?>
		<h3><?php echo $data['title'];?></h3>
	<?php endif;?>
	<?php if (!empty($data['message'])):?>
		<p><?php echo do_shortcode($data['message']);?></p>
	<?php endif;?>

		<?php if (!empty($data['items'])):?>

			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		    <script type="text/javascript">
		      google.charts.load('current', {packages:["orgchart"]});
		      google.charts.setOnLoadCallback(drawChart);

		      function drawChart() {
		        var data = new google.visualization.DataTable();
						var theParent = '<?php echo $data['parent_id'];?>';

		        data.addColumn('string', 'Name');
		        data.addColumn('string', 'Manager');
		        data.addColumn('string', 'ToolTip');

		        // For each orgchart box, provide the name, manager, and tooltip to show.

				data.addRows([
						<?php if (!empty($data['parent'])):?>
							 <?php $display_parent = '<div class="uap-mlm-tree-avatar-child uap-mlm-tree-avatar-parent"><img src="'.$data['parent_avatar'].'" /></div><div class="uap-mlm-tree-name-child">'.$data['parent_full_name'].'</div>'; ?>
							[{v:'<?php echo $data['parent_id'];?>', f:'<?php echo $display_parent;?>'}, '', ''],
						<?php endif;?>
						<?php $display_affiliate = '<div class="uap-mlm-tree-avatar-child uap-mlm-tree-avatar-main"><img src="'.$data['avatar'].'" /></div><div class="uap-mlm-tree-name-child">'.$data['full_name'].'</div>'; ?>
		          [{v:'<?php echo $data['id']; ?>', f:'<?php echo $display_affiliate;?>'}, theParent, 'Main Affiliate'],
				<?php
					foreach ($data['items'] as $item):
						$display = '<div class="uap-mlm-tree-avatar-child"><img src="'.$item['avatar'].'" /></div><div class="uap-mlm-tree-name-child">'.$item['full_name'].'</div>';
					echo "[{v:'".$item['id']."',f:'".$display."' }, '".$item['parent_id']."', '".$item['amount_value']." rewards'],";
					endforeach;
				?>
		        ]);
		        // Create the chart.
		        var chart = new google.visualization.OrgChart(document.getElementById('uap_mlm_chart'));
		        // Draw the chart, setting the allowHtml option to true for the tooltips.
						<?php if (!empty($data['parent'])):?>
						data.setRowProperty(0, 'style', 'background-color: #2a81ae; color: #fff;');
						data.setRowProperty(1, 'style', 'background-color: #f25a68; color: #fff;');
						<?php endif;?>
		        chart.draw(data, {allowHtml:true, size:"medium", allowCollapse:true});
		      }
		   </script>

<div id="uap_mlm_chart"></div>

			<table class="uap-account-table">
				<tbody>
					<thead>
						<tr>
							<th><?php _e('Subaffiliate', 'uap');?></th>
							<th><?php _e('E-mail Address', 'uap');?></th>
							<th><?php _e('Level', 'uap');?></th>
							<th><?php _e('Amount', 'uap');?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e('Subaffiliate', 'uap');?></th>
							<th><?php _e('E-mail Address', 'uap');?></th>
							<th><?php _e('Level', 'uap');?></th>
							<th><?php _e('Amount', 'uap');?></th>
						</tr>
					</tfoot>
					<?php foreach ($data['items'] as $item):?>
					<tr>
						<td><?php echo $item['username'];?></td>
						<td><?php echo $item['email'];?></td>
						<td><?php echo $item['level'];?></td>
						<td><?php echo $item['amount_value'];?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		<?php else : ?>
			<div class="uap-account-detault-message">
              <div><?php _e('In order to have affiliates inisde your MLM Matrix just promote the affiliate program and bring new affiliates registered with your Affiliate Link.', 'uap');?></div>
          </div>
		<?php endif;?>

</div>
