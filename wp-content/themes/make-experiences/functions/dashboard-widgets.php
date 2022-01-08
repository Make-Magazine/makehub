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
		//var_dump($user);
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
			if($subscription->status == "active") {
				$subscribe_date = date("Y/m/d H:i:s", strtotime($subscription->created_at));
				$expire_date = isset($subscription->expires_at) ? date("Y/m/d H:i:s", strtotime($subscription->expires_at)) : 'Never';
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
				$return .= '    <li><a href="'. $user->domain . 'membership/" class="btn universal-btn">See More Details</a></li>';
				$return .= '  </ul>';
				$return .= '</div>';
			}
		}
	}
	return $return;
}

//////////////////////////////////////
//     Maker Shed Orders widget     //
//////////////////////////////////////
function return_makershed_widget(){
  $return       = '';
  global $user_email;

  $api_url      = 'https://4e27971e92304f98d3e97056a02045f1:32e156e38d7df1cd6d73298fb647be72@makershed.myshopify.com';
  $customer_api = $api_url . '/admin/customers/search.json?query=email:"' . $user_email /* 'ken@nmhu.edu' */ . '"&fields=id';
  $customer_content = basicCurl($customer_api);

  // Decode the JSON in the file
  $customer = ((isset($customer_content) && !empty($customer_content)) ? json_decode($customer_content, true) : '');

  ?>
  <div class="dashboard-box expando-box">
      <h4 class="open"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/makershed-logo.jpg" /> Orders</h4>
      <ul class="open">
          <?php
          if (!empty($customer['customers'])) {
              $customerID = $customer['customers'][0]['id'];
              $orders_api = $api_url . '/admin/orders.json?customer_id=' . $customerID;
              $orders_content = basicCurl($orders_api);
              $orderJson = json_decode($orders_content, true);
			  //var_dump($orderJson);
              if ( empty($orderJson["orders"]) ) {
                  ?>
                  <li>
                      <p>Looks like you haven't placed any orders yet...</p><br />
                      <a href="https://makershed.com" target="_blank" class="btn universal-btn">Here's your chance!</a>
                  </li>
                  <?php
              } else {
                  foreach ($orderJson['orders'] as $order) {
                      ?>
                      <li><p><b><a href="<?php echo $order['order_status_url']; ?>">Order #<?php echo $order['id']; ?></a></b></p>
                          <?php
                          foreach ($order['line_items'] as $line_item) {
                              ?>
                              <p><?php echo $line_item['name'] ?> - $<?php echo $line_item['price']; ?></p>
                              <?php
                          }
                          ?>
                      </li>
                      <?php
                  }
              }
          } else {
              ?>
              <li>
                  <p>Looks like you haven't placed any orders yet...</p><br />
                  <a href="https://makershed.com" target="_blank" class="btn universal-btn">Here's your chance!</a>
              </li>
              <?php
          }
          ?>
      </ul>
  </div>

  <?php
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

////////////////////////////////////////
//       Makerspace List Widget       //
////////////////////////////////////////
function return_makerspace_widget(){
  global $wpdb;
  global $user_email;

  //pull makerspace information from the makerspace site
  $sql = 'SELECT meta_key, meta_value from wp_3_gf_entry_meta '
          . ' where entry_id = (select entry_id FROM `wp_3_gf_entry_meta` '
          . '                    WHERE `meta_key` LIKE "141" and meta_value like "' . $user_email . '")';
  $ms_results = $wpdb->get_results($sql);

  if (!empty($ms_results)) {
      ?>
      <div class="dashboard-box expando-box">
          <h4 class="open">My &nbsp;&nbsp;<img src="https://makerspaces.make.co/wp-content/universal-assets/v1/images/makerspaces-logo.jpg" /></h4>
          <ul class="open">
              <li><p><b><?php echo $ms_results[0]->meta_value; ?></b> - <a href="<?php echo $ms_results[1]->meta_value; ?>" target="_blank"><?php echo $ms_results[1]->meta_value; ?></a></p></li>
              <?php if ($user_id != 0 && $type == 'makerspaces') { ?>
                <li><a href="/members/<?php echo $user_slug; ?>/makerspace_info/" class="btn universal-btn">See More Details</a></li>
              <?php } ?>
              <li><a href="https://makerspaces.make.co/edit-your-makerspace/" class="btn universal-btn">Manage your Makerspace Listing</a></li>
          </ul>
      </div>
      <?php
  }
}

///////////////////////////////////////////////
//       Event Espresso Events Widget        //
//  List all events submitted by this user   //
///////////////////////////////////////////////
function return_ee_events_widget(){
  global $user_email;
  $hosted_events = EEM_Event::instance()->get_all(
          array(
              //'limit' => 10,
              'order_by' => array('EVT_visible_on' => 'DESC'),
              array(
                  'Person.PER_email' => $user_email
              )
          )
  );
  if (!empty($hosted_events)) {
      ?>
      <div class="dashboard-box expando-box">
          <h4 class="open"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/makercampus-logo.jpg" /> Facilitator</h4>
          <ul class="open">
              <?php
              foreach ($hosted_events as $event) {
                  ?>
                  <li><b><?php echo $event->name(); ?></b> - <a href="<?php echo $event->get_permalink(); ?>">View</a></li>
                  <?php
              }
              ?>
              <li><a class="btn universal-btn" href="/facilitator-portal/">Facilitator Portal</a></li>
          </ul>
      </div>
      <?php
  }
}
///////////////////////////////////////////////
//       Event Espresso Tickets Widget       //
//  List all tickets purchased by this user  //
///////////////////////////////////////////////
function return_ee_tickets_widget($user){
  global $user_email;
  //if this individual is an attenddee
  $events = EEM_Event::instance()->get_all(array(array('Attendee.ATT_email' => $user_email)));
  if ($events) {
      ?>
      <div class="dashboard-box expando-box" style="width:100%">
          <h4 class="open"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/makercampus-logo.jpg" />Tickets</h4>
          <ul class="open">
              <li>
                  <div class="espresso-my-events evetn_section_container">
                      <div class="espresso-my-events-inner-content">
                          <table class="espresso-my-events-table event_section_table">
                              <thead>
                                  <tr>
                                      <th width="45%" scope="col" class="espresso-my-events-event-th">Event Group</th>
                                      <th width="35%" scope="col" class="espresso-my-events-datetime-range-th">When</th>
                                      <th width="5%" scope="col" class="espresso-my-events-tickets-num-th">#</th>
                                      <th width="15%" scope="col" class="espresso-my-events-actions-th">Attendee(s)</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php
                                  $return = "";
                                  foreach ($events as $event) {
                                      $return .= build_ee_ticket_section($event, $user_email);
                                  }
                                  echo $return;
                                  ?>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </li>
          </ul>
      </div>
      <?php
  }
}

///////////////////////////////////////////////
//           Maker Camp Widget               //
//  Adventures enrolled & favorite content   //
///////////////////////////////////////////////
function return_makercamp_widget($user){
  $user_id   = $user->ID;
  $group_id = BP_Groups_Group::group_exists("maker-camp-2021");

  if (groups_is_user_member($user_id, $group_id)) {
      ?>
      <div class="dashboard-box expando-box" style="width:100%">
          <h4 class="open"><img src="https://makercamp.com/wp-content/themes/makercamp-theme/assets/img/makercamp-logo.png" /></h4>
          <ul class="open">
              <li>
                  <?php
                  $prev_blog_id = get_current_blog_id();

                  //switch to makercamp blog
                  switch_to_blog(7);

                  echo do_shortcode('[favorite_content]');
                  echo do_shortcode('[ld_course_list mycourses="true" num="10"]');

                  //switch back to main blog
                  switch_to_blog($prev_blog_id);
                  ?>
              </li>
          </ul>
      </div>
      <?php
  }
}

function build_ee_ticket_section($event, $user_email) {
    // if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
    $firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event($event->ID(), true, false, 1)->start();
    $now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
    $now = $now->format('Y-m-d H:i:s');
    $past_event = (date('Y-m-d H:i:s', $firstExpiredDate) < $now ? TRUE : FALSE);
    $registrations = $event->get_many_related('Registration', array(array('Attendee.ATT_email' => $user_email)));
    $time_range = espresso_event_date_range('', '', '', '', $event->ID(), FALSE);
    //get group link
    $group_id = get_field('group_id', $event->ID());
    $group = groups_get_group(array('group_id' => $group_id));
    $group_link = bp_get_group_link($group);

    //build the inner rows
    $return = '<tr class="ee-my-events-event-section-summary-row">
                    <td>' . $group_link . '</td>
                    <td>' . $time_range . '</td>
                    <td>' . count($registrations) . ' </td>
                    <td>';
    foreach ($registrations as $registration) {
        if (!$registration instanceof EE_Registration) {
            continue;
        }
        $actions = array();
        $link_to_edit_registration_text = esc_html__('Link to edit registration.', 'event_espresso');
        $link_to_make_payment_text = esc_html__('Link to make payment', 'event_espresso');
        $link_to_view_receipt_text = esc_html__('Link to view receipt', 'event_espresso');
        $link_to_view_invoice_text = esc_html__('Link to view invoice', 'event_espresso');

        //attendee name
        $attendee = $registration->attendee();
        $return .= $attendee->full_name() . '<br/>';

        if (!$past_event) {
            // only show the edit registration link IF the registration has question groups.
            $actions['edit_registration'] = $registration->count_question_groups() ? '<a aria-label="' . $link_to_edit_registration_text
                    . '" title="' . $link_to_edit_registration_text
                    . '" href="' . $registration->edit_attendee_information_url() . '">'
                    . '<span class="ee-icon ee-icon-user-edit ee-icon-size-16"></span></a>' : '';

            // resend confirmation email.
            $resend_registration_link = add_query_arg(
                    array('token' => $registration->reg_url_link(), 'resend' => true),
                    null
            );
        }

        // make payment?
        if ($registration->is_primary_registrant() && $registration->transaction() instanceof EE_Transaction && $registration->transaction()->remaining()) {
            $actions['make_payment'] = '<a aria-label="' . $link_to_make_payment_text
                    . '" title="' . $link_to_make_payment_text
                    . '" href="' . $registration->payment_overview_url() . '">'
                    . '<span class="dashicons dashicons-cart"></span></a>';
        }

        // receipt link?
        if ($registration->is_primary_registrant() && $registration->receipt_url()) {
            $actions['receipt'] = '<a aria-label="' . $link_to_view_receipt_text
                    . '" title="' . $link_to_view_receipt_text
                    . '" href="' . $registration->receipt_url() . '">'
                    . '<span class="dashicons dashicons-media-default ee-icon-size-18"></span></a>';
        }

        // ...and echo the actions!
        if (!empty($actions))
            $return .= implode('&nbsp;', $actions) . '<br/>';
    }

    $return .= '    </td>
                </tr>';

    return $return;
}
