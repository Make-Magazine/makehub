<?php 
/* example of using BP_Group_Extension
 * replace 'test' with something else -  remember that it is case-sensitive
 * checks for any selected Group Types in the 'Settings' step
 */
 
function landing_hub_add_group_extension() {

	if ( bp_is_active( 'groups' ) ) :

		class Landing_Hub_Group_Extension extends BP_Group_Extension {

			function __construct() {

				$args = array(
					'slug'              => 'group-landing',
					'name'              =>  __( 'Landing Hub Settings', 'buddypress' ),
					'nav_item_position' => 200,
					'show_tab'          => 'noone',
					'screens' => array(
						'edit' => array(
							'name'      => __( 'Landing Hub Settings', 'buddypress' ),
						),
						'create'        => array( 'position' => 10, ),
					),
				);
				parent::init( $args );
			}

			function display( $group_id = NULL ) {
				$group_id = bp_get_group_id();

				$group_extension_landing_hub = groups_get_groupmeta( $group_id, 'group_extension_setting' );
				echo 'Landing Hub: ' . esc_attr( $group_extension_landing_hub );

			}

			function settings_screen( $group_id = NULL ) {
			    $blog_id = groups_get_groupmeta( $group_id, 'landing_hub_blog_id'  );
				$subsites = get_sites();
				?>
				<p>Choose a subdomain of make.co and a post id on that subdomain to serve as the main landing page, or hub page of this group</p>
				<h4><?php _e( 'Subsites', 'buddypress' ); ?></h4>
				
				<select name="landing_hub_blog_id" id="landing_hub_blog_id">
					<option value="-1"><?php _e( '--Select--', 'buddypress-subsite' ); ?></option>
				<?php
				     // if you need details about each group type, get data as an object
				     if ( is_array( $subsites ) ) {
    					foreach ( $subsites as $subsite ) {
    					    var_dump($subsite);
    					    ?><option value="<?php echo $subsite->blog_id; ?>" <?php echo (( $subsite->blog_id == $blog_id )) ? 'selected' : ''; ?>><?php echo $subsite->domain; ?></option><?php
    					}
				     }

			        ?>
			    </select>
			    <br />
			    <h4><?php _e( 'Post ID', 'buddypress' ); ?></h4>
				<?php $post_id = groups_get_groupmeta( $group_id, 'landing_hub_post_id'  ); ?>
					<input type="text" name="landing_hub_post_id" id="landing_hub_post_id" value="<?php echo $post_id; ?>" />&nbsp;
				</div>
				<br>
				<hr />
				<?php
			}

			function settings_screen_save( $group_id = NULL ) {
				//$setting = isset( $_POST['group_extension_setting'] ) ? '1' : '0';
			    groups_update_groupmeta( $group_id, 'landing_hub_blog_id', $_POST['landing_hub_blog_id'] );
			    groups_update_groupmeta( $group_id, 'landing_hub_post_id', $_POST['landing_hub_post_id'] );
			}

		}

		bp_register_group_extension( 'Landing_Hub_Group_Extension' );

	endif;

}
add_action('bp_init', 'landing_hub_add_group_extension');