<div class="uap-top-message-new-extension">
	<?php echo _e('Extend your <strong>Ultimate Affiliate Pro</strong> system with extra features and functionality. Check additional available <strong>Extensions</strong> <a href="https://store.wpindeed.com" target="_blank">here</a>','uap'); ?>
</div>

<?php foreach ($data['feature_types'] as $k=>$v):?>
	<div class="uap-magic-box-wrap <?php echo ($v['enabled']) ? '' : 'uap-disabled-box';?>">
		<a href="<?php echo $v['link'];?>" <?php if($k == 'new_extension'): echo ' target="_blank" '; endif;?>>
			<div class="uap-magic-feature <?php echo $k;?> <?php echo $v['extra_class'];?>">
				<div class="uap-magic-box-icon"><i class="fa-uap <?php echo $v['icon'];?>"></i></div>
				<div class="uap-magic-box-title"><?php echo $v['label'];?></div>
				<div class="uap-magic-box-desc"><?php echo $v['description'];?></div> 
			</div>
		</a>	
	</div>
<?php endforeach;?>
<div class="uap-clear"></div>