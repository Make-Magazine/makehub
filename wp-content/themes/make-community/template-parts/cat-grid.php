
<li class="box-blog-entry" data-post-id="<?php the_ID(); ?>">	
    <div class="box-blog-thumb">
    	<?php if ( $args['image']!='' ) { ?>
		<a target="_none" href="<?php echo $args['link']; ?>"><?php echo $args['image']; ?></a>
		<?php } ?>
	</div>

	<div class="box-blog-details">
		<a target="_none" href="<?php echo $args['link']; ?>">
			<?php $thetitle = $args['title']; $getlength = strlen($thetitle); $thelength = 120; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?>				
		</a>

		<div class="box-blog-details-bottom">
			<span class="box-blog-details-bottom-author">			
				<?php echo $args['author']; ?>
			</span>			
			<div class="clear"></div>
		</div><!-- box-blog-details-bottom -->
	</div><!-- box-blog-details -->

</li>