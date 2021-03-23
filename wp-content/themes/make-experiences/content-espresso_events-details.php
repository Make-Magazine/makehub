<?php
//echo '<br/><h6 style="color:#2EA2CC;">'. __FILE__ . ' &nbsp; <span style="font-weight:normal;color:#E76700"> Line #: ' . __LINE__ . '</span></h6>';
global $post;
$relevants = get_field('events');

?>
<div class="event-content container-fluid">
	<div class="row">
<?php if ( apply_filters( 'FHEE__content_espresso_events_details_template__display_entry_meta', TRUE )): ?>
	<div class="entry-meta col-sm-12">
		<span class="tags-links"><?php espresso_event_categories( $post->ID, TRUE, TRUE ); ?></span>
	<?php
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
	?>
	<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'event_espresso' ), __( '1 Comment', 'event_espresso' ), __( '% Comments', 'event_espresso' ) ); ?></span>
	<?php
		endif;
		edit_post_link( __( 'Edit', 'event_espresso' ), '<span class="edit-link">', '</span>' );
	?>
	</div>
<?php endif;
	$event_phone = espresso_event_phone( $post->ID, FALSE );
	if ( $event_phone != '' ) : ?>
	<p class="event-phone col-sm-12">
		<span class="small-text"><strong><?php esc_html_e( 'Event Phone:', 'event_espresso' ); ?> </strong></span> <?php echo $event_phone; ?>
	</p>
<?php endif;  ?>
<?php
	if ( apply_filters( 'FHEE__content_espresso_events_details_template__display_the_content', true ) ) {
		do_action( 'AHEE_event_details_before_the_content', $post ); ?>
		<div class='event-dates col-sm-12'>
			<?php echo espresso_list_of_event_dates(); ?>
		</div>
		<?php
			if(class_exists('ESSB_Plugin_Options')){ 
				$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				echo do_shortcode('[easy-social-share buttons="facebook,pinterest,reddit,twitter,linkedin,love,more" morebutton_icon="dots" morebutton="2" counters="yes" counter_pos="after" total_counter_pos="hidden" animation="essb_icon_animation6" style="icon" fullwidth="yes" template="6" postid="' . $event_id . '" url="' . $url . '" text="' . get_the_title() . '"]');
			}
		?>
		<div class='event-main-content col-md-8 col-sm-12 col-xs-12'>
			<?php
			echo apply_filters(
				'FHEE__content_espresso_events_details_template__the_content',
				get_the_content()
			); ?>
			<!-- ACF FIELDS GO HERE -->
		</div>
		
		<div class='event-sidebar-content col-md-4 col-sm-12 col-xs-12'>
			<div class="event-sidebar-item">
				<?php echo do_shortcode("[ESPRESSO_TICKET_SELECTOR event_id=".$post->ID."]");	?>
			</div>
		    <?php // ACF FIELDS GO HERE --> 
			if( $relevents && is_singular( array( 'espresso_events') ) ){ ?>
				<div class="related-events">
				<h3 class="event-venues-h3 ee-event-h3">Related Events</h3>
				<ul>
					<?php foreach( $relevents as $relevent ): ?>
						<li>
							<a href="<?php echo get_permalink( $relevent->ID ); ?>">
								<?php echo get_the_title( $relevent->ID ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				</div>
			<?php } ?>
		</div>
		<?php
		do_action( 'AHEE_event_details_after_the_content', $post );
	}
 ?>
	</div>
</div>
<!-- .event-content -->
