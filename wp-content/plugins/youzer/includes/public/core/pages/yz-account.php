<?php

class Youzer_Account {

	function __construct() {

		// Get Settings.
		add_action( 'bp_init', array( &$this, 'settings' ) );

		// Delete Account
		add_action( 'bp_actions', array( &$this, 'delete_account' ) );
	}

	/**
	 * Settings.
	 */
	function settings ()  {

		if ( ! yz_is_account_page() ) {
			return;
		}

		// Is Avatar Page.
		// add_action( 'bp_init', array( &$this, 'avatar_is_front_edit' ) );

		// Rename Account Tabs.
		add_action( 'bp_actions', array( &$this, 'rename_tabs' ), 10 );

		// Add Account Settings Pages.
		add_action( 'bp_actions', array( &$this, 'account_setting_menus' ) );
	
		// Settings Sidebar Menu
		add_action( 'youzer_settings_menus', array( &$this, 'settings_header' ) );

		// Change Icons.
		add_filter( 'yz_account_menu_icon', array( &$this, 'get_account_menu_icon' ), 10, 2 );

		//  Settings Scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'settings_scripts' ) );

		if ( bp_is_current_component( 'profile' ) ) {
			// Redirect Default Tab.
			add_action( 'bp_screens', array( &$this, 'redirect_to_default_profile_settings_tab' ), 10 );
			// Get User Settings.
			add_action( 'youzer_profile_settings', array( &$this, 'get_profile_settings' ) );
		}

		if ( bp_is_current_component( 'settings' ) ) {
			// Get Account Settings
			add_action( 'youzer_profile_settings', array( &$this, 'get_account_settings' ) );
		}

		if ( bp_is_current_component( 'widgets' ) ) {
			// Get Widgets Settings
			add_action( 'youzer_profile_settings', array( &$this, 'get_widgets_settings' ) );
		}

	}
	/**
	 * Setup Widget Settings Pages.
	 */
	function account_setting_menus() {

	    if ( ! bp_core_can_edit_settings() ) {
	        return false;
	    } 
	    
	    global $bp, $Youzer;

	    if ( bp_is_active( 'settings' ) ) {

		    // Profile Widgets Settings.
		    bp_core_new_nav_item(
		        array( 
		            'position' => 60,
		            'slug' => 'widgets', 
		            'parent_slug' => $bp->profile->slug,
		            'show_for_displayed_user' => bp_core_can_edit_settings(),
		            'default_subnav_slug' => 'widgets',
		            'name' => __( 'Widgets Settings', 'youzer' ), 
		            'screen_function' => 'yz_profile_widgets_settings_tab_screen', 
		            'parent_url' => bp_loggedin_user_domain() . '/widgets/'
		        )
		    );

		    // Add Widgets Pages The Settings Page List.
		    foreach ( $Youzer->widgets->get_settings_widgets() as $page ) {

		        bp_core_new_subnav_item( array(
		                'slug' => $page['widget_name'],
		                'name' => $page['widget_title'],
		                'parent_slug' => 'widgets',
		                'parent_url' => yz_get_widgets_settings_url(),
		                'screen_function' => 'yz_get_profile_settings_page',
		            )
		        );
		    }

		    foreach ( $this->account_settings_pages() as $slug => $page ) {

		        // Get Navbar Args
		        $nav_args = array(
		            'slug' => $slug,
		            'name' => $page['name'],
		            'parent_url' => yz_get_settings_url(),
		            'parent_slug' => bp_get_settings_slug(),
		            'screen_function' => 'yz_get_profile_settings_page',
		        );

		        if ( 'delete-account' == $slug ) {
		            $nav_args['user_has_access'] = ! is_super_admin( bp_displayed_user_id() );
		        }

		        bp_core_new_subnav_item( $nav_args );
		    }

	    }

	    if ( bp_is_active( 'xprofile' ) ) {

		    foreach ( $this->profile_settings_pages() as $slug => $page ) {

		        bp_core_new_subnav_item( array(
		                'slug' => $slug,
		                'name' => $page['name'],
		                'position' => $page['order'],
		                'parent_slug' => bp_get_profile_slug(),
		                'parent_url' => yz_get_profile_settings_url(),
		                'screen_function' => 'yz_get_profile_settings_page',
		            )
		        );
		    }

		}
	}

	/**
	 * Redirect Default.
	 */
	function redirect_to_default_profile_settings_tab() {

		if ( bp_current_action() == 'public' && ! bp_action_variable( 1 ) ) {
			$redirect_url = yz_get_profile_settings_url();
			bp_core_redirect( trailingslashit( $redirect_url ) );
		}

	}
	
	/**
	 * # Account Settings Pages.
	 */
	function get_account_menu_icon( $icon = null, $slug ) {

		switch ( $slug ) {
			case 'general':
				$icon = 'fas fa-lock';
				break;
			case 'notifications':
				$icon = 'fas fa-bell';
				break;
				
			case 'change-username':
				return $icon = 'fas fa-sync-alt';
			
			case 'account-deactivator':
				return $icon = 'fas fa-user-cog';

			case 'blocked':
				return $icon = 'fas fa-ban';
		}

		return $icon;
	}

	/**
	 * # Account Settings Pages.
	 */
	function account_settings_pages() {

		// Init Pages.
		$pages = array();

		// Add Spam Account nav item.
		if ( bp_current_user_can( 'bp_moderate' ) && ! bp_is_my_profile() ) {

			$pages['capabilities'] = array(
				'name'	=> __( 'Capabilities Settings', 'youzer' ),
				'icon'	=> 'fas fa-wrench',
				'order'	=> 50
			);

		}

	    if ( 'on' == yz_options( 'yz_allow_private_profiles' ) || 'on' == yz_options( 'yz_enable_woocommerce' ) ) {
	    	$pages['account-privacy'] = array(
				'name'	=> __( 'Account Privacy', 'youzer' ),
				'icon'	=> 'fas fa-user-secret',
				'order'	=> 60
			);
	    }

	    if ( apply_filters( 'bp_settings_show_user_data_page', true ) ) {

			$pages['data'] = array(
				'name'	=> __( 'Export Data', 'youzer' ),
				'icon'	=> 'fas fa-file-export',
				'order'	=> 80
			);

	    }

	    if ( ( ! bp_disable_account_deletion() && bp_is_my_profile() ) || bp_current_user_can( 'delete_users' ) ) {
	    	if ( ! is_super_admin( bp_displayed_user_id() ) ) {	
		    	$pages['delete-account'] = array(
					'name'	=> __( 'Delete account', 'youzer' ),
					'icon'	=> 'fas fa-trash-alt',
					'order'	=> 60
				);
	    	}
	    }

		// Filter
		$pages = apply_filters( 'yz_account_settings_pages', $pages );

	    return $pages;
	}

	/**
	 * # Profile Settings Pages.
	 */
	function profile_settings_pages() {
		    
        if ( bp_is_active( 'xprofile' ) ) {

            // Fields Groups.
            $groups = bp_profile_get_field_groups();
            $i = 1;
            foreach ( $groups as $group ) {

                // Hide Empty Fields Groups
                if ( count( $group->fields ) <= 0 ) {
                    continue;
                }
                
                $group_slug = 'edit/group/' . $group->id;

                // Prepare Item Data.
                $page_item = array(
                    'name'  => $group->name,
                    'widget_name'   => $group->name,
                    'icon'  => yz_get_xprofile_group_icon( $group->id ),
                    'order' => 10 * $i
                );

                // Add Groups Pages List.
                $pages[ $group_slug ] = $page_item;
                $i++;
            }

        }

        if ( isset( $pages['change-avatar'] ) ) {
        	unset( $pages['change-avatar'] );
        }
        
        if ( isset( $pages['change-cover-image'] ) ) {
        	unset( $pages['change-cover-image'] );
        }

        if ( apply_filters( 'yz_display_user_account_profile_avatar_cover_pages', false ) ) {

	        if ( buddypress()->avatar->show_avatars ) {

		        $pages['change-avatar'] = array(
		            'name'  => __( 'Profile Picture', 'youzer' ),
		            'icon'  => 'fas fa-user-circle',
		            'order' => 200
		        );

	        }
	        
	        if ( bp_displayed_user_use_cover_image_header() ) {

		        $pages['change-cover-image'] = array(
		            'name'  => __( 'Profile Cover', 'youzer' ),
		            'icon'  => 'fas fa-camera-retro',
		            'order' => 210
		        );
	        
	        }
        }
    
        $pages['social-networks'] = array(
            'name'  => __( 'Social Networks', 'youzer' ),
            'icon'  => 'fas fa-share-alt',
            'order' => 230
        );

        // Filter
        $pages = apply_filters( 'yz_profile_settings_pages', $pages );
        
        return $pages;
	}

	/**
	 * # Profile Settings Menu.
	 */
	function profile_menu() {

		// Get Menu Data.
		$menu_settings = array(
			'slug'		=> 'profile',
			'menu_list'  => $this->profile_settings_pages(),
			'menu_title' => __( 'Profile Settings', 'youzer' )
		);

		$this->get_menu( $menu_settings, 'profile' );

	}

	/**
	 * # Account Settings Menu.
	 */
	function account_menu() {

		// Get Menu Data.
		$menu_settings = array(
			'slug'		=> 'settings',
			'menu_list'  => $this->account_settings_pages(),
			'menu_title' => __( 'Account Settings', 'youzer' )
		);

		$this->get_menu( $menu_settings, 'settings' );

	}

	/**
	 * # Widgets Settings Menu.
	 */
	function widgets_menu() {

		global $Youzer;

		// Get Widgets Menu List.
		$menu_list = $Youzer->widgets->get_settings_widgets();

		// Filter.
		$menu_list = apply_filters( 'account_widgets_settings_pages', $menu_list );

		// Prepare Account Menu List
		$menu_settings = array(
			'slug'		 => 'widgets',
			'menu_title' => __( 'Widgets Settings', 'youzer' ),
			'menu_list'	 => $menu_list
		);

		// Print Menu's
		$this->get_menu( $menu_settings, 'widgets' );

	}

	/**
	 * Convert Widgets to Pages.
	 */
	function convert_widgets_to_pages( $widgets ) {

		$pages = null;

		foreach ( $widgets as $widget ) {

			// Get Widget Key.
			$key = $widget['widget_name'];

			// Get Page Data.
			$pages[ $key ] = array(
				'name' => $widget['widget_title'],
				'icon' => $widget['widget_icon']
			);

		}
		return $pages;
	}

	/**
	 * # Menu Content
	 */
	function get_menu( $args, $current_component ) {

		// Get Menu.
		$menu = $args['menu_list'];

		// Get Current Page.
		$current = bp_current_action();

		// Get Current Widget Name.
		if ( 'widgets' == $current_component ) {
			$current_widget = bp_current_action() && bp_current_action() != bp_current_component() ? bp_current_action() : $menu[0]['widget_name'];
			$menu = $this->convert_widgets_to_pages( $menu );
		} elseif ( 'edit' == $current ) {

	        // Get Widget Name.
	        $current_widget = 'edit/group/' . bp_get_current_profile_group_id();

		} else {
			$current_widget = $current;	
		}

	    // Get Buddypress Variables.
	    $bp = buddypress();

	    // Get Tab Navigation  Menu
	    $account_nav = $bp->members->nav->get_secondary( array( 'parent_slug' => $current_component ) );

	    // Show Menu

	    $show_menu = bp_is_current_component( $current_component ) ? 'yz-account-menus yz-show-account-menus' : 'yz-account-menus';

	    echo "<div class='$show_menu'>";
	    echo "<ul>";

	    // Hide Following Pages For Menus.
		$hide_pages = array( 'classic', 'home', 'social-networks', 'change-avatar', 'change-cover-image' );
	  
	    // Get Menu.
		foreach ( $account_nav as $page ) {

			// Get Page Slug.
			$slug = $page['slug'];

			// Hide Pages & Hide Tab if user has no access
	        if ( in_array( $slug, $hide_pages ) || empty( $page['user_has_access'] ) || 'edit' == $slug  ) {
	        	continue;
	        }

			// Get Menu Data.
			$menu_data = isset( $menu[ $slug ] ) ? $menu[ $slug ] : null;

			// Get Menu Class Name.	
			$menu_class = ( $current_widget == $slug ) ? 'class="yz-active-menu"' : null;

			// Get Page Url.
			if ( isset( $page['group_slug'] ) ) {
				$page_url = yz_get_profile_settings_url( $page['group_slug'] );
			} elseif ( 'settings' == $args['slug'] ) {
				$page_url = yz_get_settings_url( $slug );
			} elseif ( 'profile' == $args['slug'] ) {
				$page_url = yz_get_profile_settings_url( $slug );
			} elseif ( 'widgets' == $args['slug'] ) {
				$page_url = yz_get_widgets_settings_url( $slug );
			}

			// Filter URL.
			$page_url = apply_filters( 'yz_account_menu_page_url', $page_url, $slug );

			// Get Icon
			$icon = isset( $menu_data['icon'] ) ? $menu_data['icon'] : 'fas fa-cogs';

			// Filter Icon.
			$icon = apply_filters( 'yz_account_menu_icon', $icon, $slug );

			$class = str_replace( '/', '-', $slug );

			echo '<li class="yz-' . $class .  '">';
			echo '<i class="'. $icon. '"></i>';
			echo "<a $menu_class href='$page_url'>{$page['name']}</a>";
			echo '</li>';

		}

	    echo '</ul></div>';

	}
	
	/**
	 * # Get Custom Widgets Menu List
	 */
	function custom_widgets_menus() {

		// Get Custom Widgets
		$custom_widgets = yz_options( 'yz_custom_widgets' );

		if ( empty( $custom_widgets ) ) {
			return false;
		}

		// Create new Array.
		$wg_menu_list = array();

		foreach ( $custom_widgets as $widget => $data ) {

        	// Check if Custom Widget have at least one field.
			if ( ! yz_check_custom_widget( $widget ) ) {
				continue;
			}

			// Prepare Menu item Then Add it to menu List
			$wg_menu_list[] = array(
				'widget_name' 	=> $widget,
				'widget_title' 	=> $data['name'],
				'widget_icon' 	=> $data['icon'],
				'menu_order'  	=> 1
			);

		}

		return $wg_menu_list;

	}

	/**
	 * # Settings Header.
	 */
	function settings_header() {

		// Get Data.
		$account_pages = $this->get_account_main_menu();
		$icon_url = yz_get_profile_settings_url( 'change-avatar' );
		$member_year = date( 'Y', strtotime( yz_data( 'user_registered' ) ) );
		$header_buttons = apply_filters( 'yz_account_menu_header_buttons', array(
			'home' => array(
				'icon' => 'fas fa-home',
				'title' => __( 'home', 'youzer' ),
				'url' => home_url()
			),
			'profile' => array(
				'icon' => 'fas fa-user',
				'title' => __( 'View Profile', 'youzer' ),
				'url' => bp_core_get_user_domain( bp_displayed_user_id() )
			),
			'networks' => array(
				'icon' => 'fas fa-share-alt',
				'title' => __( 'social networks', 'youzer' ),
				'url' => yz_get_profile_settings_url( 'social-networks' )
			),
			'avatar' => array(
				'icon' => 'fas fa-user-circle',
				'title' => __( 'profile avatar', 'youzer' ),
				'url' => yz_get_profile_settings_url( 'change-avatar' )
			),
			'cover' => array(
				'icon' => 'fas fa-camera-retro',
				'title' => __( 'profile cover', 'youzer' ),
				'url' => yz_get_profile_settings_url( 'change-cover-image' ) 
			),
			'logout' => array(
				'url' => wp_logout_url(),
				'icon' => 'fas fa-power-off',
				'title' => __( 'Logout', 'youzer' )
			)
		) );

        if ( ! buddypress()->avatar->show_avatars ) {
        	if ( isset(  $header_buttons['avatar'] ) ) {
        		unset( $header_buttons['avatar'] );
        	}
        }

        if ( ! bp_displayed_user_use_cover_image_header() ) {
        	if ( isset(  $header_buttons['cover'] ) ) {
        		unset( $header_buttons['cover'] );
        	}
        }

        // if there's no networks don't show the networks form..
        $networks = get_option( 'yz_social_networks' );
        
        if ( empty( $networks ) ) {
            unset( $header_buttons['networks'] );
        }

        $count = count( $header_buttons );
		
		global $Youzer;
		?>

		<div class="yz-account-header">

			<div class="yz-account-head">
				<div class="yz-account-img">
					<?php echo bp_core_fetch_avatar( array( 'item_id' => bp_displayed_user_id(), 'type' => 'full' ) ); ?>
					
				</div>
				<div class="yz-account-head-content">
					<h2><?php echo bp_get_displayed_user_fullname(); ?></h2>
					<span><?php printf( esc_html__( 'member since %1$s', 'youzer' ), $member_year ); ?></span>
				</div>
			</div>

			<div class="yz-head-buttons">
				<div class="yz-head-buttons-inner">
				<?php foreach ( $header_buttons as $key => $button ) :?>
					<div class="yz-button-item yz-<?php echo $key; ?>-button" style="width: <?php echo 100 / $count; ?>%;"><a href="<?php echo $button['url'] ?>" data-yztooltip="<?php echo $button['title']; ?>" ><i class="<?php echo $button['icon'] ?>"></i></a></div>
				<?php endforeach;?>
				</div>
			</div>

			<div class="yz-account-settings-menu">
				<?php foreach ( $account_pages as $key => $page ) : ?>
				
				<?php 
					if ( isset( $page['visibility'] ) && false == $page['visibility'] ) {
						continue;
					}
				?>

				<div class="yz-account-menu">
	                <div class="yz-menu-icon"><i class="<?php echo $page['icon'] ?> <?php echo $page['class'] ?>"></i></div>
	                <div class="yz-menu-head">
	                    <div class="yz-menu-title"><?php echo $page['title']; ?></div>
	                    <div class="yz-menu-description"><?php echo $page['description']; ?></div>
	                </div>
	               	<div class="yz-arrow-bottom yzpc-show-fields"></div>
	            </div>
	            <?php 
	            	if ( $key == 'profile' ) {
	            		$this->profile_menu();
	            	} elseif( $key == 'widgets' ) {
	            		$this->widgets_menu();
	            	} elseif( $key == 'settings' ) {
	            		$this->account_menu();
	            	}
	            ?>
				<?php  ?>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}

	/**
	 * Get Profile Settings
	 */
	function get_profile_settings() {

    	global $Youzer;
	    	
	    // Get Current Sub Page.
		$page = bp_current_action();
	    
	    switch ( $page ) {

			// Edit
			case 'edit':
	            $Youzer->widgets->basic_infos->group_fields();
				break;

			case 'change-avatar':
	            $Youzer->widgets->basic_infos->profile_picture();
				break;	      

			case 'change-cover-image':
	            $Youzer->widgets->basic_infos->profile_cover();
				break;	      

			case 'social-networks':
	            $Youzer->widgets->social_networks->settings();
				break;

			default:
				bp_get_template_part( 'members/single/plugins' );
				break;
	    }
	}

	/**
	 * Account Page Main Menu
	 */
	function get_account_main_menu() {

		// Init Menu
		$menu =  array();

		// Profile Settings Page
		$profile = array( 'profile' => array(
			'icon' => 'fas fa-user-circle',
			'class' => 'yza-profile-settings',
			'href' => yz_get_profile_settings_url(),
			'title' => __( 'profile settings', 'youzer' ),
			'description' => __( 'Profile Information Fields', 'youzer' )
		) );

		// Account Settings Page
		$account = array( 'settings' => array(
			'icon' => 'fas fa-cogs',
			'href' => yz_get_settings_url(),
			'class' => 'yza-account-settings',
			'visibility' => bp_is_active('settings'),
			'title' => __( 'account settings', 'youzer' ),
			'description' => __( 'Email, Password, Notifications ...', 'youzer' )
		) );

		// Widgets Settings Page
		$widgets = array( 'widgets' => array(
			'icon' => 'fas fa-sliders-h',
			'class' => 'yza-widgets-settings',
			'href' => yz_get_widgets_settings_url(),
			'title' => __( 'widgets settings', 'youzer' ),
			'description' => __( 'Profile Widgets Settings', 'youzer' )
		) );

		// Get Current Component.
        $current_component = bp_current_component();

        if ( $current_component == 'profile' ) {
        	$menu = array_merge( $profile, $account, $widgets );
        } elseif ( $current_component == 'settings' ) {
        	$menu = array_merge( $account, $profile, $widgets );
        } elseif ( $current_component == 'widgets' ) {
        	$menu = array_merge( $widgets, $profile, $account );
        }

		// Filter
		$menu = apply_filters( 'yz_account_page_main_menu', $menu );

		return $menu;

	}
	/**
	 * Get Account Settings
	 */
	function get_account_settings() {

    	global $Youzer;
	    	
	    switch ( bp_current_action() ) {
  
			case 'capabilities':
	            $Youzer->widgets->basic_infos->user_capabilities();
				break;

			case 'delete-account':
	            $Youzer->widgets->basic_infos->delete_account();
				break;

			case 'account-privacy':
	            $Youzer->widgets->basic_infos->account_privacy();
				break;	      	      

			case 'general':
	            $Youzer->widgets->basic_infos->general();
				break;

			case 'data':
	            $Youzer->widgets->basic_infos->data();
	            break;
	            
			case 'export-data':
	            $Youzer->widgets->basic_infos->export_data();
				break;

			case 'notifications':
	            $Youzer->widgets->basic_infos->notifications_settings();
				break;

			case 'change-username':
	            $Youzer->widgets->basic_infos->change_username();
				break;

			case 'account-deactivator':
	            $Youzer->widgets->basic_infos->account_deactivator();
				break;

			case 'blocked':
	            $Youzer->widgets->basic_infos->members_block();
				break;

			default:            	
				bp_get_template_part( 'members/single/plugins' );
				break;
	    }
	}

	/**
	 * Get Widgets Settings
	 */
	function get_widgets_settings() {

    	global $Youzer;
	    	
	    // Get Current Sub Page.
		$page = bp_current_action();
	    
	    switch ( $page ) {

			case 'slideshow':
			case 'instagram':
			case 'portfolio':
			case 'services':
			case 'about_me':
			case 'project':
			case 'flickr':
			case 'skills':
			case 'quote':
			case 'video':
			case 'link':
			case 'post':
				
	            $Youzer->widgets->$page->settings();
				break;
				
			default:

				if ( isset( $Youzer->widgets->$page ) ) {
	            	$Youzer->widgets->$page->settings();
				} else {

		    		// Get Widgets Settings.
		    		$widgets_menu = $Youzer->widgets->get_settings_widgets();
		    		// Set First Widget Form Menu as Default.
		    		$default_widget = $widgets_menu[0]['widget_name'];
		    		// Print Widget Settings.
	            	$Youzer->widgets->$default_widget->settings();
			    	
				}
				
				break;	      

		}

	}
	
	/**
	 * # Settings Actions.
	 */
	function rename_tabs() {

		if ( bp_is_active( 'settings' ) ) {


			$bp = buddypress();
	
			// Get Settings Slug.
			$settings_slug = bp_get_settings_slug();

			// Remove Settings Profile, General Pages
			bp_core_remove_subnav_item( $settings_slug, 'profile' );

			// Change Notifications Title from "Email" to "Notifications".
			$bp->members->nav->edit_nav(array('name' => __( 'Notifications', 'youzer' ) ), 'notifications', $settings_slug );
			$bp->members->nav->edit_nav(array('name' => __( 'Email & Password', 'youzer' ) ,  'position' => 1 ), 'general', $settings_slug );

		}

		// Remove Profile Public, Edit Pages
		bp_core_remove_subnav_item( bp_get_profile_slug(), 'public' );

	}

	/**
	 * Handles the deleting of a user.
	 */
	function delete_account() {

	    // Bail if not a POST action.
	    if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
	        return;

	    // Bail if no submit action.
	    if ( ! isset( $_POST['yz-delete-account-understand'] ) )
	        return;

	    // // Bail if not in settings.
	    if ( ! bp_is_settings_component() || 'delete-account' != bp_current_action() )
	        return false;

	    // 404 if there are any additional action variables attached
	    if ( bp_action_variables() ) {
	        bp_do_404();
	        return;
	    }

	    // Bail if account deletion is disabled.
	    if ( bp_disable_account_deletion() && ! bp_current_user_can( 'delete_users' ) ) {
	        return false;
	    }

	    // Nonce check.
	    check_admin_referer( 'delete-account' );

	    // Get username now because it might be gone soon!
	    $username = bp_get_displayed_user_fullname();

	    // Delete the users account.
	    if ( bp_core_delete_account( bp_displayed_user_id() ) ) {

	        // Add feedback after deleting a user.
	        bp_core_add_message( sprintf( __( '%s was successfully deleted.', 'youzer' ), $username ), 'success' );

	        // Redirect to the root domain.
	        bp_core_redirect(  bp_get_root_domain() );
	    }
	}

	/**
	 * Activate Avatar Upload On Front Page.
	 */
	function avatar_is_front_edit(){
	    // Get Current Sub Page.
	    if ( 'profile-picture' == bp_current_action() ) {
	        add_filter( 'bp_avatar_is_front_edit', 'yz_filter_change_avatar_template' );
	    }
	}

	/*
	 * # Account Scripts .
	 */
	function settings_scripts() {
	
		global $Yz_Translation;

	    // Widgets Builder
	    wp_register_script( 'yz-builder', YZ_PA . 'js/ukai-builder.min.js', array( 'jquery-ui-sortable', 'jquery-ui-draggable' ), YZ_Version, true );

        // Load Profile Settings CSS.
        wp_enqueue_style( 'yz-account-css', YZ_PA . 'css/yz-account-style.min.css', array( 'yz-panel-css' ), YZ_Version );

	    // Admin Panel Script
	    wp_enqueue_script( 'yz-panel', YZ_PA . 'js/yz-settings-page.min.js', array( 'jquery' ), YZ_Version, true );

	    // Localize Script Object ( yz = youzer ).
	    wp_localize_script( 'yz-panel', 'yz', $Yz_Translation );

        // Load Profile Settings Script
        wp_enqueue_script( 'yz-account', YZ_PA . 'js/yz-account.min.js', array( 'jquery', 'yz-scrolltotop' ), YZ_Version, true );

	}

	/*
	 * # ColorPicker Scripts .
	 */
	function colorpicker_scripts() {

	    wp_enqueue_style( 'wp-color-picker' );

	    wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), YZ_Version, true );

	    wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), YZ_Version, true );

	    $colorpicker_translate = array(
	        'clear' 		=> __( 'Clear', 'youzer' ),
	        'defaultString' => __( 'Default', 'youzer' ),
	        'pick' 			=> __( 'Select Color', 'youzer' ),
	        'current' 		=> __( 'Current Color', 'youzer' ),
	    );

	    wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_translate );

	}

}