<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Admin_Controller')){
	class Wpvc_Admin_Controller{

		public static $addScript = false;
		public function __construct(){
				add_action('init', array($this,'wpvc_votes_register_taxonomy'));
				add_action('admin_menu', array($this, 'wpvc_voting_admin_menu'));
				add_action('parent_file', array($this,'wpvc_tax_menu_correction'));
				add_action( 'admin_init', array( $this, 'setup_wizard' ) );
				add_action('wp_after_admin_bar_render',array($this,'wpvc_check_migrated'));
				// Add scripts 
				add_action( 'admin_enqueue_scripts',array( $this, 'wpvc_add_voting_admin_tools' ));	

				add_action('add_meta_boxes', array($this,'wpvc_category_meta_box_contestant'));				
				add_action('save_post_contestants', array($this,'wpvc_customlink_meta_box'), 10, 3);
				add_filter('use_block_editor_for_post_type', array($this,'wpvc_disable_guttenburg'), 10, 2);
				
				add_action('admin_bar_menu', array($this,'wpvc_voting_custom_toolbar_link'), 999);
				add_action( 'widgets_init', array($this,'wpvc_voting_sidebar_init'));

				add_action('restrict_manage_posts',array($this,'wpvc_filter_by_taxonomy'));
				add_filter('parse_query',array($this,'wpvc_taxonomy_filter_query'));

				add_filter('admin_body_class', array($this,'wpvc_add_class_before_migration'));
				add_action('wp_trash_post',array($this,'wpvc_delete_contestant_track'));

				// Add the Settings link to a plugin on Plugins page.
				add_filter( 'plugin_action_links_' . plugin_basename( WPVC_VOTES_SL_PLUGIN_FILE ), array($this,'wpvc_plugin_action_link'), 10, 5 );

				add_filter('post_row_actions',array($this,'wpvc_remove_quick_actions'),10,2);
				add_filter( 'bulk_actions-edit-'.WPVC_VOTES_TYPE, array($this,'wpvc_remove_bulk_edit_actions') );


		}

		//Remove QuickEdit
		public function wpvc_remove_quick_actions($actions, $post ){
			if ($post->post_type== WPVC_VOTES_TYPE) {
				unset($actions['inline hide-if-no-js']);
			}
			return $actions;
		}

		//Setup Wizard
		public function setup_wizard(){   
			if ( get_option( $opt_name = "votes_enable_setup_wizard" ) ) {
				delete_option( $opt_name );
				wp_redirect( admin_url( '/index.php?page=votes-setup' ));

			}
			return;
		}

		//Remove Bulk Edit
		public function wpvc_remove_bulk_edit_actions($actions){
			unset( $actions[ 'edit' ] );
			return $actions;
		}


		public function wpvc_check_migrated(){
			global $pagenow;
			$page = isset($_GET['page'])?$_GET['page']:'';
			if(($pagenow=='admin.php' || $pagenow=='edit.php') && ($_GET['page']=='contestants' || strpos($page, 'wpvc') !== false || $_GET['post_type']=='contestants'))
			{
				$check_migrate = Wpvc_Migration_Model::wpvc_check_migration_process();
				$setting_check = get_option(WPVC_VOTES_SETTINGS);
				if(!isset($setting_check['common']) && $check_migrate==0){
					Wpvc_Common_Settings_Controller::wpvc_voting_migration();
				}
			}
			return;
		}

		public function wpvc_add_class_before_migration($classes){
			global $pagenow;
			$page = isset($_GET['page'])?$_GET['page']:'';
			if(($pagenow=='admin.php' || $pagenow=='edit.php') && ($_GET['page']=='contestants' || strpos($page, 'wpvc') !== false || $_GET['post_type']=='contestants'))
			{	
				$setting_check = get_option(WPVC_VOTES_SETTINGS);
				if($page != 'wpvc_migration'){
					$check_migrate = Wpvc_Migration_Model::wpvc_check_migration_process();
					if($check_migrate==0 && $setting_check==''){
						$classes = 'wpvc_migrate_before_voting';
						return $classes;
					}
				}
			}
			return $classes;
		}

		//Widget add
		public function wpvc_voting_sidebar_init() {
			register_sidebar( array(
				'name' => 'Contestants Sidebar',
				'id' => 'contestants_sidebar',
				'before_widget' => '<div class="wpvc_contestants_sec_sidebar">',
				'after_widget' => '</div>',
				'before_title' => '<h2 class="wpvc_contestests_sidebar">',
				'after_title' => '</h2>'
			));
			register_widget('Wpvc_Widget_Leader_Controller');
			register_widget('Wpvc_Widget_Recent_Controller');
		}

		//Register taxonomy and post type
		public function wpvc_votes_register_taxonomy(){
				$menupos=26; // This helps to avoid menu position conflicts with other plugins.
				$cust_slug	=  get_option(WPVC_VOTES_SETTINGS);
				$slug	=  isset($cust_slug['vote_custom_slug'])?$cust_slug['vote_custom_slug']:'';
				$slug 	=  ($slug == null)?'contestants':$slug;
				while (isset($GLOBALS['menu'][$menupos])) $menupos+=1;

				$labels = array(
					'name'               => _x( 'Contestants', 'post type general name', 'voting-contest' ),
					'singular_name'      => _x( 'Contestant', 'post type singular name', 'voting-contest' ),
					'menu_name'          => _x( 'Contestants', 'admin menu', 'voting-contest' ),
					'name_admin_bar'     => _x( 'Contestant', 'add new on admin bar', 'voting-contest' ),
					'add_new'            => _x( 'Add New', 'Contestant', 'voting-contest' ),
					'add_new_item'       => __( 'Add New Contestant', 'voting-contest' ),
					'new_item'           => __( 'New Contestant', 'voting-contest' ),
					'edit_item'          => __( 'Edit Contestant', 'voting-contest' ),
					'view_item'          => __( 'View Contestant', 'voting-contest' ),
					'all_items'          => __( 'All Contestants', 'voting-contest' ),
					'search_items'       => __( 'Search Contestants', 'voting-contest' ),
					'parent_item_colon'  => __( 'Parent Contestants:', 'voting-contest' ),
					'not_found'          => __( 'No Contestants found.', 'voting-contest' ),
					'not_found_in_trash' => __( 'No Contestants found in Trash.', 'voting-contest' )
				);
				$args = array(
					'labels'             => $labels,
					'description'        => __( 'Description.', 'voting-contest' ),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_rest'       => true, // Set to true for Guttenburg Editor
					'show_in_menu'       => false,
					'query_var'          => true,
					'rewrite'            => array( 'slug' => $slug ),
					'capability_type'    => 'post',
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => $menupos,
					'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
				);

				register_post_type( WPVC_VOTES_TYPE, $args );
				flush_rewrite_rules();

				$labels = array(
					'name'                       => _x( 'Contest Category', 'taxonomy general name', 'voting-contest' ),
					'singular_name'              => _x( 'Contest Category', 'taxonomy singular name', 'voting-contest' ),
					'search_items'               => __( 'Search Contest Category', 'voting-contest' ),
					'popular_items'              => __( 'Popular Contest Category', 'voting-contest' ),
					'all_items'                  => __( 'All Contest Category', 'voting-contest' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __( 'Edit Contest Category', 'voting-contest' ),
					'update_item'                => __( 'Update Contest Category', 'voting-contest' ),
					'add_new_item'               => __( 'Add New Contest Category', 'voting-contest' ),
					'new_item_name'              => __( 'New Contest Category Name', 'voting-contest' ),
					'separate_items_with_commas' => __( 'Separate Contest Category with commas', 'voting-contest' ),
					'add_or_remove_items'        => __( 'Add or remove Contest Category', 'voting-contest' ),
					'choose_from_most_used'      => __( 'Choose from the most used Contest Category', 'voting-contest' ),
					'not_found'                  => __( 'No Contest Category found.', 'voting-contest' ),
					'menu_name'                  => __( 'Contest Category', 'voting-contest' ),
				);

				register_taxonomy(WPVC_VOTES_TAXONOMY,
						array(WPVC_VOTES_TYPE),
						array(
							'hierarchical' => true,
							'labels'       => $labels,
							'show_ui'      => false,
							'query_var'    => true,
							'publicly_queryable' => true,
							'public'  => true,
							'show_admin_column'  => true,
							'rewrite'  => true,
							'show_in_rest'  => true, // Set to true for Guttenburg Editor			
							'meta_box_cb'  => array($this,'wpvc_contest_category_meta_box',1,2),
						)
				);

				$vote_opt = get_option(WPVC_VOTES_SETTINGS);
				if(isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {
					do_action( 'wpvc_vote_twitter_auth_hook', $vote_opt );
				}
				
		}

    	//Admin menu start
		public function wpvc_voting_admin_menu(){

			$common_ctrl = new Wpvc_Common_Settings_Controller();
			//$licence_ctrl = new Wpvc_License_Controller();
			add_dashboard_page( '', '', 'manage_options', 'votes-setup', '' );
			add_menu_page('Contests-Voting', 'Contest', 'manage_options',WPVC_VOTES_TYPE, array( $common_ctrl, 'wpvc_voting_overview'),'dashicons-awards',90);
			
			add_submenu_page(WPVC_VOTES_TYPE, __('Overview','voting-contest'), __('Overview','voting-contest'), 'manage_options', WPVC_VOTES_TYPE,array( $common_ctrl, 'wpvc_voting_overview'));

			add_submenu_page(WPVC_VOTES_TYPE, __('Contest Category','voting-contest'), "<span class='vote_contest_cat'>".__('Contest Category','voting-contest')."</span>", 'publish_pages', 'wpvc_category', array( $common_ctrl,'wpvc_voting_category_list'));

			add_submenu_page(WPVC_VOTES_TYPE, __('Contestants','voting-contest'), "<span class='vote_contest_contestants'>".__('Contestants','voting-contest')."</span>", 'publish_pages', 'edit.php?post_type=contestants', '');
			
			apply_filters('ow_contestant_menu_bottom','');
			
			add_submenu_page('', __('Add Contestant','voting-contest'), __('Add Contestant','voting-contest'), 'publish_pages', 'post-new.php?post_type=contestants', '');
			add_submenu_page('', __('Move Contestant','voting-contest'), __('Move Contestant','voting-contest'), 'publish_pages', 'wpvc_move_posts', array( $common_ctrl,'wpvc_voting_move_contestants'));
			add_submenu_page('', __('Import Contestants','voting-contest'), __('Import Contestant','voting-contest'), 'publish_pages', 'wpvc_votes_csv', array( $common_ctrl,'wpvc_voting_import_contestants'));
			add_submenu_page('', __('Export Contestants','voting-contest'), __('Export Contestant','voting-contest'), 'publish_pages', 'wpvc_votes_export', array( $common_ctrl,'wpvc_voting_export_contestants'));
			add_submenu_page('', __('Voting Logs','voting-contest'), __('Voting Logs','voting-contest'), 'publish_pages', 'wpvc_votinglogs', array( $common_ctrl,'wpvc_voting_vote_logs'));
			add_submenu_page('', __('Migrate Voting','voting-contest'), __('Migrate Voting','voting-contest'), 'publish_pages', 'wpvc_migration', array( $common_ctrl,'wpvc_voting_migration'));

			//add_submenu_page('', __('Votes Setup','voting-contest'), __('Votes Setup','voting-contest'), 'publish_pages', 'votes-setup', array( $common_ctrl,'wpvc_voting_setup_wizard'));
			

			add_submenu_page(WPVC_VOTES_TYPE, __('Clear Voting Entries','voting-contest'), __('Clear Voting Entries','voting-contest'), 'publish_pages', 'wpvc_votes_purge', array($common_ctrl,'wpvc_voting_clear_voting_entries'));

			add_submenu_page(WPVC_VOTES_TYPE, __('Custom Fields','voting-contest'), "<span class='vote_contest_cat'>".__('Custom Fields','voting-contest')."</span>", 'publish_pages', 'wpvc_custom_fields', array( $common_ctrl,'wpvc_voting_custom_fields'));

			add_submenu_page(WPVC_VOTES_TYPE, __('Registration Fields','voting-contest'), "<span class='vote_contest_cat'>".__('Registration Fields','voting-contest')."</span>", 'publish_pages', 'wpvc_registration_fields', array( $common_ctrl,'wpvc_voting_registration_custom_fields'));

			add_submenu_page(WPVC_VOTES_TYPE, __('Settings','voting-contest'), "<span class='setting_vote_page'>".__('Settings','voting-contest')."</span>", 'publish_pages', 'wpvc_settings', array( $common_ctrl,'wpvc_voting_setting_common'));

			if(is_plugin_active('wp-voting-contest-paidentry/wpvc_paidentry.php')){
				add_submenu_page(WPVC_VOTES_TYPE, __('Entry Orders','voting-contest'), "<span class='setting_vote_page'>".__('Entry Orders','voting-contest')."</span>", 'publish_pages', 'edit.php?post_type=ow_payment', '');
			}			

			if(is_plugin_active('wp-voting-contest-buyvotes/ow_votes_buy_votes.php')){
				add_submenu_page(WPVC_VOTES_TYPE, __('Buy Vote Orders','voting-contest'), "<span class='vote_contest_buy_paypal'>".__('Buy Vote Orders','voting-contest')."</span>", 'publish_pages', 'edit.php?post_type=ow_buy_payment', '');
			}

			add_submenu_page(WPVC_VOTES_TYPE, __('Plugin Translations','voting-contest'), __('Translations','voting-contest'), 'publish_pages', 'wpvc-votes-translations', array($common_ctrl,'wpvc_voting_software_translations_page'));
			
		 	add_submenu_page(WPVC_VOTES_TYPE, __('Plugin License','voting-contest'), __('License','voting-contest'), 'publish_pages', 'wpvc-votes-license', array($common_ctrl,'wpvc_voting_software_license_page'));
		}

		public function wpvc_tax_menu_correction($parent_file){
			global $current_screen,$submenu_file;		   
		    $base = $current_screen->base;
		    $action = $current_screen->action;
		    $post_type = $current_screen->post_type;

		    //Admin menu selection not a right way
		    if($post_type == WPVC_VOTES_TYPE || $base == 'admin_page_wpvc_move_posts' || $base == 'admin_page_wpvc_votes_csv' || $base == 'admin_page_wpvc_votes_export' || $base == 'admin_page_wpvc_votinglogs'){ ?>
			    <script type="text/javascript">
					jQuery(document).ready( function($)
					{
						jQuery('li#toplevel_page_contestants').removeClass('wp-not-current-submenu');
						jQuery('li#toplevel_page_contestants').addClass('wp-has-current-submenu');
						jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').removeClass('wp-not-current-submenu');
						jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').addClass('wp-has-current-submenu');
						var reference = $('.vote_contest_contestants').parent().parent();
						// add highlighting to our custom submenu
						reference.addClass('current');
						//remove higlighting from the default menu
						reference.parent().find('li:first').removeClass('current');
						
					});
			    </script>
			    <?php
		    }
			if($post_type == 'ow_payment' || $post_type == 'ow_buy_payment'){ ?>
				<script type="text/javascript">
					jQuery(document).ready( function($)
					{
						jQuery('li#toplevel_page_contestants').removeClass('wp-not-current-submenu');
						jQuery('li#toplevel_page_contestants').addClass('wp-has-current-submenu');
						jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').removeClass('wp-not-current-submenu');
						jQuery('li#toplevel_page_contestants a.toplevel_page_contestants').addClass('wp-has-current-submenu');
						var reference = $('.vote_contest_contestants').parent().parent();
						//remove higlighting from the default menu
						reference.parent().find('li:first').removeClass('current');
						
					});
			    </script>
				<?php

			}
		    return $parent_file;
		}
		
    	// Add CSS
		public function wpvc_add_voting_admin_tools(){
			wp_enqueue_style( 'ow-wpvc-admin-css', plugins_url( 'assets/css/ow-wpvc-admin-css.css' , dirname(__FILE__) ),'',time() );
			wp_register_style('ow_tabs_setting', plugins_url( 'assets/css/ow_tabs.css' , dirname(__FILE__) ),'',time());
			wp_enqueue_style('ow_tabs_setting');
			//Add Builded React JS 
			wp_enqueue_script(
				'wpvc-owfront-runtime-admin',
				plugins_url('/wpvc_views/build/runtime.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			wp_enqueue_script(
				'wpvc-owadmin-vendor',
				plugins_url('/wpvc_views/build/vendors.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			wp_enqueue_script(
				'wpvc-owadmin-react',
				plugins_url('/wpvc_views/build/admin.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);
			$query_args = array(
				'family' => 'Open+Sans:400,500,700|Oswald:700|Roboto:300,400,500,700',
				'subset' => 'latin,latin-ext',
			);
			$query_icons= array(
				'family' => 'Material+Icons'
			);
			wp_register_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
			wp_enqueue_style('google_fonts');
			wp_register_style( 'material_icons', add_query_arg( $query_icons, "//fonts.googleapis.com/icon" ), array(), null );
			wp_enqueue_style('material_icons');
		}

		public function wpvc_category_meta_box_contestant(){
			remove_meta_box( 'contest_categorydiv', WPVC_VOTES_TYPE, 'side' );
			add_meta_box('votescategory', __('Contest Category','voting-contest'), array($this,'wpvc_contest_category_meta_box'), WPVC_VOTES_TYPE, 'side', 'high',array( '__block_editor_compatible_meta_box' => true));
		}
		
		public function wpvc_contest_category_meta_box($post,$box){
			$terms = get_terms( WPVC_VOTES_TAXONOMY, array( 'hide_empty' => false ) );
			$post  = get_post();
			$rating = wp_get_object_terms( $post->ID, WPVC_VOTES_TAXONOMY, array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
			$term_id  = '';

			if ( ! is_wp_error( $rating ) ) {
				if ( isset( $rating[0] ) && isset( $rating[0]->term_id ) ) {
					$term_id = $rating[0]->term_id;
				}
			}

			foreach ( $terms as $term ) {
		?>
				<label title='<?php esc_attr_e( $term->name ); ?>'>
					<input type="radio" name="contest_category" value="<?php esc_attr_e( $term->term_id ); ?>" <?php checked( $term->term_id, $term_id ); ?>>
					<span><?php esc_html_e( $term->name ); ?></span>
				</label><br>
		<?php
			}

		}

		public function wpvc_voting_custom_toolbar_link($wp_admin_bar){

			//In Front End for Edit Contestant
			if(is_singular(WPVC_VOTES_TYPE)){
				$args = array(
					'id' => 'edit_contestant',
					'title' => 'Edit Contestant',
					'href' =>  get_edit_post_link() ,
					'meta' => array(
						'class' => 'edit_contestant',
						'title' => __('Edit Contestant','voting-contest')
						)
				);
				$wp_admin_bar->add_node($args);
			}
		}

		public function wpvc_customlink_meta_box($post_id, $post, $update){
			$slug = WPVC_VOTES_TYPE;
			// If this isn't a 'contestants' post, don't update it.
			if ( $slug != $post->post_type ) {
				return;
			}
	
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['contest_category'] ) ) {
				return;
			}

			update_post_meta($post_id, 'contestant-title', $post->post_title);
			update_post_meta($post_id, 'contestant-desc', $post->post_content);
			if(isset($_POST['ow_contestant_link'])){
				update_post_meta($post_id, WPVC_CONTESTANT_LINK, $_POST['ow_contestant_link']);
			}
			$get_val = get_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD,true);
			if($get_val=='')
				update_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD, 0);
		}
		
		//Disable Guttenburg Editor in admin end 
		public function wpvc_disable_guttenburg($gutenberg_filter, $post_type){
			if ($post_type === WPVC_VOTES_TYPE) 
				return false;
			return $gutenberg_filter;
		}

		//Add filter for taxonomy in Admin 
		public function wpvc_filter_by_taxonomy(){
			global $typenow;
			global $wp_query;
			if ($typenow == WPVC_VOTES_TYPE) {
				$taxonomy = WPVC_VOTES_TAXONOMY;
				$voting_taxonomy = get_taxonomy($taxonomy);
				$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
				wp_dropdown_categories(array(
					'show_option_all' =>  __("Show All {$voting_taxonomy->label}"),
					'taxonomy'        =>  $taxonomy,
					'name'            =>  WPVC_VOTES_TAXONOMY,
					'orderby'         =>  'name',
					'selected'        =>  $selected,
					'hierarchical'    =>  true,
					'depth'           =>  3,
					'show_count'      =>  true, // Show # listings in parens
					'hide_empty'      =>  true, // Don't show businesses w/o listings
				));
			}
		}

		//Filter Query for Taxonomy while selecting Taxonomy Dropdown
		public function wpvc_taxonomy_filter_query($query){
			global $pagenow;
			$post_type = WPVC_VOTES_TYPE; 
			$taxonomy  = WPVC_VOTES_TAXONOMY; 
			$q_vars    = &$query->query_vars;
			if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
				$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
				$q_vars[$taxonomy] = $term->slug;
			}
		}

		public function wpvc_plugin_action_link( $actions, $plugin_file ){
			$settings   = array('settings'  => '<a href="'.admin_url().'admin.php?page=wpvc_settings">' . __('Settings', 'voting-contest') . '</a>');
			$site_link  = array('support'   => '<a href="http://plugins.ohiowebtech.com" target="_blank">' . __('Support', 'voting-contest') . '</a>');
			$setup_wizard  = array('wizard'   => '<a href="'.admin_url().'admin.php?page=votes-setup">' . __('Wizard', 'voting-contest') . '</a>');
			$migration_link  = array('migration'   => '<a style="font-weight:700" href="'.admin_url().'admin.php?page=wpvc_migration">' . __('Migrate', 'voting-contest') . '</a>');

			$actions = array_merge($setup_wizard, $actions);
			$actions = array_merge($migration_link, $actions);
			$actions = array_merge($settings, $actions);
			$actions = array_merge($site_link, $actions);
						
			return $actions;
		}

		//Delete Post Entry track 
		public function wpvc_delete_contestant_track($post_id){
			$post_type = get_post_type( $post_id );
			$post_status = get_post_status( $post_id );
			if ( $post_type == WPVC_VOTES_TYPE && in_array($post_status, array( 'publish','draft','future','pending' ))){
				Wpvc_Shortcode_Model::wpvc_delete_post_entry_track($post_id);
			}
		}

	}


}
else
die("<h2>".__('Failed to load the Voting Admin Controller','voting-contest')."</h2>");

return new Wpvc_Admin_Controller();
