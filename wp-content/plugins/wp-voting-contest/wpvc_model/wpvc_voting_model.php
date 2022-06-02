<?php
if(!class_exists('Wpvc_Voting_Model')){
	class Wpvc_Voting_Model {

		public static function wpvc_save_votes($pid,$termid,$votesetting,$post_index,$manual=null, $buy_vote = false, $buyvote_count = 1,$email= null){
			global $wpdb;
			$button_count = get_term_meta($termid,'vote_count_per_cat',true);


			$tracking_method = $votesetting['vote_tracking_method'];
			$vote_count =  get_term_meta($termid,'vote_count_per_cat',true);

			$freq_count = $votesetting['vote_frequency_count'];
			$frequency =  $votesetting['frequency'];
			$voting_type = $votesetting['vote_votingtype'];
			
			//No Limit
			if($frequency==0 && $freq_count > 1){
				if($vote_count < $freq_count){
					$vote_count = $freq_count;
				}
			}

			if($manual==null){
				$is_votable = SELF::wpvc_check_is_votable($pid,$termid,$votesetting,$post_index,$email);
			}else{
				$is_votable = 1;
				$vote_count =$post_index; // on manual votes count sent on postindex param
			}
			
			if($buy_vote){
				$vote_count = $buyvote_count;
			}

			if($is_votable == 1 || $buy_vote){
				switch($tracking_method){
					case 'cookie_traced':
						$useragent = SELF::wpvc_cookie_voting_getBrowser();
						$voter_cookie = $useragent['name'].'@'.$termid;
						$ip_addr = $voter_cookie;
					break;
	
					case 'email_verify':
						if($votesetting['user_logged'] == true){
							$current_user = get_user_by('id', $votesetting['user_id_profile']);
							$ip_addr = $current_user->user_email;
						}
						else{
							$ip_addr = $email;
						}
					break;
	
					case 'ip_email':
						if($votesetting['user_logged'] == true){
							$current_user = get_user_by('id', $votesetting['user_id_profile']);
							$ip_addr = $current_user->user_email;
						}
						else{
							$ip_addr = $email;
						}
					break;
	
					case 'ip_traced':
						$ip_addr = SELF::wpvc_getUserIpAddr();
					break;
				}

				$ip_always = SELF::wpvc_getUserIpAddr();

				if($votesetting['user_logged'] == false && $votesetting['vote_grab_email_address'] == 'on' && $votesetting['onlyloggedinuser'] == 'off'){
					$email_always = $email;					
				}
				else{					
					$current_user = get_user_by('id', $votesetting['user_id_profile']);
					$email_always = $current_user->user_email;
					if($votesetting['user_id_profile'] == null){
						$email_always = $email;		
					}
				}

				if($manual=='manual'){
					$ip_addr = $manual;
				}
				
				//Update Vote count
				SELF::wpvc_update_vote_contestant($ip_addr,$vote_count,$pid,$termid,$ip_always,$email_always);

				$response = SELF::wpvc_get_vote_response($pid,$termid,$votesetting,$post_index, $email_always);
				return $response;
			}else{
				return $is_votable;
			}
			
		}

		private static function wpvc_get_vote_response($pid,$termid,$votesetting,$post_index, $email_always){
			$freq_count = $votesetting['vote_frequency_count'];
			$voting_type = $votesetting['vote_votingtype'];
			$hourscalc = $votesetting['vote_frequency_hours'];
			$frequency =  $votesetting['frequency'];
			

			$total_count = SELF::wpvc_get_total_vote_count($pid);	
			$is_votable = SELF::wpvc_check_is_votable($pid,$termid,$votesetting);
			$voted = "voted";
	
			$resp_val = '';
			//Frequency count 
			if($freq_count < 1){
				$freq_count = 1;
			}

			$buy_vote = false;
			$buy_vote = get_option('_wpvc_buyvotes_extension') == 1 ? true : false;
            if($buy_vote){
				$buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
				if($buy_votes_status == 'on'){
					$buy_check = SELF::wpvc_check_is_votable($pid,$termid,$votesetting,$post_index, $email_always);
					$buy_vote = array_key_exists('buy_vote', $buy_check['update_vote_count']) ? $buy_check['update_vote_count']['buy_vote'] : false;
				}
			}

			switch($frequency){
				//No Limit
				case '0':
					if($voting_type==0){
						$resp_val = 'single';
					}else{
						$resp_val = 'multiple';
					}
				
					$response = array('update_vote_count'=>array(
						'voting_count'=>$total_count,
						'count_post_id'=>$pid,
						'post_index'=>$post_index,
						'msg'=>$voted,
						'cat_select'=>'no_limit',
						'resp_val'=>$resp_val,
						'freq_count'=>$freq_count,
						'buy_vote' => $buy_vote,
						'is_votable' => $is_votable
					));
					return $response;
				break;
				//Per Calendar day
				case '1':
					$post_ids =0;
					if($voting_type==0){
						$resp_val = 'single';
					}else{
						$resp_val = 'multiple';
						$post_ids = Wpvc_Voting_Save_Model::wpvc_get_voted_post_ids_date($pid,$termid,"calendar");
					}
					$response = array('update_vote_count'=>array(
						'voting_count'=>$total_count,
						'count_post_id'=>$pid,
						'post_index'=>$post_index,
						'msg'=>$voted,
						'cat_select'=>'per_calendar',
						'resp_val'=>$resp_val,
						'freq_count'=>$freq_count,
						'post_ids'=>$post_ids,
						'buy_vote' => $buy_vote,
						'is_votable' => $is_votable
					));
					return $response;

				break;
				//Per Hours
				case '2':
					$post_ids =0;
					if($voting_type==0){
						$resp_val = 'single';
					}else{
						$resp_val = 'multiple';
						$post_ids = Wpvc_Voting_Save_Model::wpvc_get_voted_post_ids_date($pid,$termid,"hours",$hourscalc);
					}
					$response = array('update_vote_count'=>array(
						'voting_count'=>$total_count,
						'count_post_id'=>$pid,
						'post_index'=>$post_index,
						'msg'=>$voted,
						'cat_select'=>'per_hour',
						'resp_val'=>$resp_val,
						'freq_count'=>$freq_count,
						'post_ids'=>$post_ids,
						'buy_vote' => $buy_vote,
						'is_votable' => $is_votable
					));
					return $response;
				break;

				//Per Hours
				case '11':
					$post_ids =0;
					if($voting_type==0){
						$resp_val = 'single';
					}else{
						$resp_val = 'multiple';
						$post_ids = Wpvc_Voting_Save_Model::wpvc_get_voted_post_ids_date($pid,$termid,"category");
					}
					$response = array('update_vote_count'=>array(
						'voting_count'=>$total_count,
						'count_post_id'=>$pid,
						'post_index'=>$post_index,
						'msg'=>$voted,
						'cat_select'=>'per_category',
						'resp_val'=>$resp_val,
						'freq_count'=>$freq_count,
						'post_ids'=>$post_ids,
						'buy_vote' => $buy_vote,
						'is_votable' => $is_votable
					));
					return $response;
				break;

			}

			
			
		}

		private static function wpvc_check_is_votable($post_id,$termid,$votesetting,$post_index=null,$email= null){
			if($post_index==''){
				$term_settings = SELF::wpvc_get_term_settings($termid);
				if(!empty($term_settings)){
					$end_time = $term_settings['votes_expiration'];
					$start_time = $term_settings['votes_starttime'];
					$current_time = current_time( 'timestamp', 0 );
					if(($start_time !='' && strtotime($start_time) > $current_time)){
						return 'not_started';
					}
					elseif(($end_time != '' && strtotime($end_time) < $current_time)){
						return 'ended';
					}
				}
			}

			$frequency =  $votesetting['frequency'];
			switch($frequency){
				//No Limit
				case '0':
					$no_limit = Wpvc_Voting_Save_Model::wpvc_no_limit_votes($post_id,$termid,$votesetting,$post_index,$email);
					return $no_limit;
				break;

				//Per Calendar day
				case '1':
					$per_calendar = Wpvc_Voting_Save_Model::wpvc_per_calendar_votes($post_id,$termid,$votesetting,$post_index,$email);
					return $per_calendar;
				break;
				
				//Every Hours
				case '2':
					$per_hour= Wpvc_Voting_Save_Model::wpvc_every_hour_votes($post_id,$termid,$votesetting,$post_index,$email);
					return $per_hour;
				break;

				//Per Category
				case '11':
					$per_cat= Wpvc_Voting_Save_Model::wpvc_per_category_votes($post_id,$termid,$votesetting,$post_index,$email);
					return $per_cat;
				break;
			}
									
		}

		public static function wpvc_get_term_settings($termid){
			$category_options = get_term_meta($termid);
			$align_category = array();
			$imgcontest = get_term_meta($termid,'imgcontest',true);
			$musicfileenable = get_term_meta($termid,'musicfileenable',true);
			if(is_array($category_options)){
				foreach($category_options as $key=>$val){
					if($key=='contest_rules'){
						$align_category[$key] = format_to_edit($val[0],TRUE);
					}else
						$align_category[$key] = maybe_unserialize($val[0]);
				}
			}
			return $align_category;
		}

		public static function wpvc_check_before_post($pid,$termid){
			$votesetting = get_option(WPVC_VOTES_SETTINGS);
			if(is_array($votesetting)){
				$contest_setting = $votesetting['contest'];
				$is_votable = SELF::wpvc_check_is_votable($pid,$termid,$contest_setting);
				return $is_votable;
			}
		}

		public static function wpvc_getUserIpAddr(){
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				//ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				//ip pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

		public static function wpvc_update_vote_contestant($ip,$vote_count,$pid,$termid,$ip_always = null,$email_always = null){
			global $wpdb;
			$todate = new DateTime();
            $to_date = $todate->format('Y-m-d H:i:s');
			$save_sql = 'INSERT INTO `' . WPVC_VOTES_TBL . '` (`ip` , `votes` , `post_id` , `termid` , `date` ,`ip_always`,`email_always` )
					VALUES ( "' . $ip . '", "'.$vote_count.'"  , ' . $pid . ', "'.$termid.'", "'.$to_date.'","' . $ip_always . '","' . $email_always . '" ) ';

			$wpdb->query($save_sql);
		}

		public static function wpvc_get_total_vote_count($pid){
			global $wpdb;
			$new_sql = "SELECT SUM(votes)  FROM " . WPVC_VOTES_TBL ." WHERE post_id =".$pid;
			$total_v =  $wpdb->get_var($new_sql);
			update_post_meta($pid, WPVC_VOTES_CUSTOMFIELD, $total_v);
			return $total_v;
		}

		public static function wpvc_cookie_voting_getBrowser()
		{
			$u_agent = $_SERVER['HTTP_USER_AGENT'];
			$bname = 'Unknown';
			$platform = 'Unknown';
			$version= "";

			//First get the platform?
			if (preg_match('/linux/i', $u_agent)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $u_agent)) {
				$platform = 'windows';
			}

			// Next get the name of the useragent yes seperately and for good reason
			if((preg_match('/MSIE/i',$u_agent) || preg_match('/Trident/i',$u_agent)) && !preg_match('/Opera/i',$u_agent))
			{
				$bname = 'IE';
				$ub = "MSIE";
			}
			elseif(preg_match('/Edg/i',$u_agent))
			{
				$bname = 'EDG';
				$ub = "Firefox";
			}
			elseif(preg_match('/Firefox/i',$u_agent))
			{
				$bname = 'MF';
				$ub = "Firefox";
			}
			elseif(preg_match('/Chrome/i',$u_agent))
			{
				$bname = 'GC';
				$ub = "Chrome";
			}
			elseif(preg_match('/Safari/i',$u_agent))
			{
				$bname = 'AS';
				$ub = "Safari";
			}
			elseif(preg_match('/Opera/i',$u_agent))
			{
				$bname = 'O';
				$ub = "Opera";
			}
			elseif(preg_match('/Netscape/i',$u_agent))
			{
				$bname = 'N';
				$ub = "Netscape";
			}

			// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
				// we have no matching number just continue
			}

			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}
			else {
				$version= $matches['version'][0];
			}

			// check if we have a number
			if ($version==null || $version=="") {$version="?";}

			return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'    => $pattern
			);
		}
        
	}
}else
die("<h2>".__('Failed to load Voting Entry model')."</h2>");

return new Wpvc_Voting_Model();
?>