<?php
////////////////////////////////////////
//       Membership Card Widget       //
////////////////////////////////////////
function return_membership_widget($user){
	global $user_slug;
	$return = '';
	$user_id   = $user->ID;
	$user_meta = get_user_meta($user_id);

	if ( isset($user_meta['ihc_user_levels'][0]) && class_exists( '\Indeed\Ihc\UserSubscriptions' ) ) {
		$return  = '<div class="dashboard-box expando-box">';
		$return .= '  <h4 class="open"><img style="max-width:100px;" src="'. get_stylesheet_directory_uri().'/images/make-community-logo.png" /> Membership Details</h4>';
		$return .= '  <ul class="open">';
		$return .= '    <li>'. do_shortcode('[ihc-membership-card]').'</li>';
		$return .= '    <li><a href="/members/'. $user_slug . '/membership/" class="btn universal-btn">See More Details</a></li>';
		$return .= '  </ul>';
		$return .= '</div>';
	} else if( class_exists('MeprUtils') ) {
		$mepr_current_user = MeprUtils::get_currentuserinfo();

	    $sub_cols = array('id','user_id','product_id','product_name','subscr_id','status','created_at','expires_at','active');
		$user = bp_get_displayed_user();

	    $table = MeprSubscription::account_subscr_table(
	      'created_at', 'DESC',
	      1, '', 'any', 0, false,
	      array(
	        'member' => $mepr_current_user->user_login,
	      ),
	      $sub_cols
	    );
	    $subscriptions = $table['results'];
		foreach($subscriptions as $subscription) {
			if($subscription->status == "active" || $subscription->status == "None") {
				$subscribe_date = date("Y/m/d H:i:s", strtotime($subscription->created_at));
				$expire_date = isset($subscription->expires_at) ? date("Y/m/d H:i:s", strtotime($subscription->expires_at)) : 'Never';
				if($expire_date == "-0001/11/30 00:00:00") { $expire_date = "Never"; }
				$return  = '<div class="dashboard-box expando-box">';
				$return .= '  <h4 class="open"><img style="max-width:100px;" src="'. get_stylesheet_directory_uri().'/images/make-community-logo.png" /> Membership Details</h4>';
				$return .= '  <ul class="open">';
				$return .= '    <li><div class="membership-card">';
				$return .= '        <img src="https://make.co/wp-content/uploads/2021/06/make_logo-2.svg" alt="'.$user->fullname.'\'s Membership Card">';
				$return .= '        <h3 class="mebr-name">' . $user->fullname . '</h3>';
				$return .= '        <div class="mebr-membership"><label>Level:</label> ' . $subscription->product_name .'</div>';
				$return .= '        <div class="mebr-startdate"><label>Member Since:</label> ' . $subscribe_date . '</div>';
				$return .= '        <div class="mebr-expiredate"><label>Expiration Date:</label> ' . $expire_date . '</div>';
				$return .= '    </div></li>';
				$return .= '    <li><a href="'. $user->domain . 'mp-membership/" class="btn universal-btn membership-btn">See More Details</a></li>';
				$return .= '  </ul>';
				$return .= '</div>';
			}
		}
	}
	return $return;
}


////////////////////////////////////////
//     Maker Faire Entries Widget     //
////////////////////////////////////////
function return_makerfaire_widget(){
  global $user_slug;
  global $user_email;

  //access the makerfaire database.
  include(get_stylesheet_directory() . '/db-connect/mf-config.php');
  $mysqli = new mysqli($host, $user, $password, $database);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  } else {
    $entryData = array();
    $mf_portal = false;

    //pull maker information from mf database.
    $sql = 'SELECT  wp_mf_maker_to_entity.entity_id, wp_mf_maker_to_entity.maker_type, '
    . '     wp_mf_maker_to_entity.maker_role, wp_mf_entity.presentation_title, '
    . '     wp_mf_entity.status, wp_mf_entity.faire, wp_mf_entity.project_photo, wp_mf_entity.desc_short, '
    . '     wp_mf_faire.faire_name, year(wp_mf_faire.start_dt) as faire_year '
    . 'FROM `wp_mf_maker` '
    . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
    . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id '
    . 'left outer join wp_gf_entry on wp_mf_entity.lead_id = wp_gf_entry.id  '
    . 'left outer join wp_mf_faire on wp_mf_entity.faire=wp_mf_faire.faire '
    . 'where Email like "' . $user_email . '" and wp_mf_entity.status="Accepted"  and maker_type!="contact" and wp_gf_entry.status !="trash" '
    . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    foreach ($entries as $entry) {
      $faire_name = ($entry['faire'] == 'NMF16' ? 'National Maker Faire 2016' : $entry['faire_name']);
      $entryData[] = array('entry_id' => $entry['entity_id'],
      'title' => $entry['presentation_title'],
      'faire_url' => 'makerfaire.com',
      'faire_name' => $faire_name,
      'year' => $entry['faire_year']);
    }
    if (!empty($entries)) {
      $mf_portal = true;
    }
  }

  //pull in global faires now
  include(get_stylesheet_directory() . '/db-connect/globalmf-config.php');
  $mysqli = new mysqli($host, $user, $password, $database);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  } else {
    //pull maker information from database.
    $sql = 'SELECT  wp_mf_maker_to_entity.entity_id, wp_mf_maker_to_entity.maker_type, '
    . '     wp_mf_maker_to_entity.maker_role, wp_mf_entity.presentation_title, '
    . '     wp_mf_entity.status, wp_mf_entity.faire as faire_name, wp_mf_entity.project_photo, wp_mf_entity.desc_short,'
    . '     wp_mf_entity.faire_year, wp_mf_entity.blog_id '
    . 'FROM `wp_mf_maker` '
    . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
    . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id  and wp_mf_maker_to_entity.blog_id = wp_mf_entity.blog_id '
    . 'where Email like "' . $user_email . '" and wp_mf_entity.status="Accepted"  and maker_type!="contact" '
    . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    foreach ($entries as $entry) {
      //get faire name
      $faire_sql = "SELECT option_name, option_value FROM `wp_" . $entry['blog_id'] . "_options` where option_name = 'blogname' OR option_name = 'theme_mods_MiniMakerFaire'";
      $faire_data = $mysqli->query($faire_sql) or trigger_error($mysqli->error . "[$faire_sql]");
      $faire_name = '';
      foreach ($faire_data as $fdata) {
        if ($fdata['option_name'] == 'blogname')
          $faire_name = $fdata['option_value'];
      }
      $entryData[] = array('entry_id' => $entry['entity_id'],
      'title' => $entry['presentation_title'],
      'faire_url' => $entry['faire_name'],
      'faire_name' => $faire_name . ' ' . $entry['faire_year'],
      'year' => $entry['faire_year']);
    }

    $entryDataUnique = array_unique($entryData, SORT_REGULAR);
    if (isset($entryDataUnique) && !empty($entryDataUnique)) {
      $return .= '<div class="dashboard-box expando-box">
      <h4 class="open"><img src="' . get_stylesheet_directory_uri() . '/images/makerfaire-logo.jpg" /> Entries</h4>
      <ul class="open">';
      foreach ($entryDataUnique as $entry) {
        $return .= '<li><p><b><a href="https://' . $entry['faire_url'] . '/maker/entry/' . $entry['entry_id'] . '" target="_blank">' . $entry['title'] . '</a></b> - ' . $entry['faire_name'] . '</p>';
      }
      $return .= '<li><a href="/members/' . $user_slug . '/makerfaire_info/" class="btn universal-btn">See More Details</a></li>';
      if ($mf_portal == true) {
        $return .= '<li><a href="https://makerfaire.com/manage-entries/" class="btn universal-btn">Maker Faire Portal</a></li>';
      }
      $return .= '</ul>
      </div>';
    }
  }
}
