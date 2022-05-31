<?php
if(!class_exists('Wpvc_Voting_Save_Model')){
	class Wpvc_Voting_Save_Model {

        
		public static function wpvc_check_if_voted_category($pid=null,$termid=null,$where=null,$retun=null){
			global $wpdb;

			if($pid !=null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."' && termid='".$termid."'";
			elseif($pid!=null && $termid == null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."'";
			elseif($pid==null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && termid ='".$termid."'";

			if($retun != null){
				$voted_val = $wpdb->get_results($new_sql,ARRAY_A);
				$num_rows = $wpdb->num_rows;
				return $num_rows;
			}else{
				$total_v =  $wpdb->get_var($new_sql);
				return $total_v;
			}
		}

        public static function wpvc_check_if_voted_no_limit($pid=null,$termid=null,$where=null){
			global $wpdb;

			if($pid !=null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."' && termid='".$termid."'";
			elseif($pid!=null && $termid == null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."'";
			elseif($pid==null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && termid ='".$termid."'";

            $total_v =  $wpdb->get_var($new_sql);
            return $total_v;
			
		}

        public static function wpvc_get_voted_post_ids_date($pid,$termid,$date_val=null,$hourscalc=null){
			global $wpdb;
            if($date_val == 'calendar'){
                $from = date("Y-m-d 00:00:01");
                $to = date("Y-m-d 23:59:59");
            }
            else if($date_val== 'hours'){                
                $now = new DateTime(); //current date/time
                $now->sub(new DateInterval("PT".$hourscalc."H"));
                $from = $now->format('Y-m-d H:i:s');
                $to = date("Y-m-d H:i:s");
            }
            
            if($date_val != 'category'){
                $new_sql = "SELECT post_id  FROM " . WPVC_VOTES_TBL ." WHERE date between '$from' AND '$to' && termid='".$termid."'  ORDER BY id DESC";
            }else{
                $new_sql = "SELECT post_id  FROM " . WPVC_VOTES_TBL ." WHERE termid='".$termid."'  ORDER BY id DESC";
            }
							
			$voted_val = $wpdb->get_results($new_sql,ARRAY_A);
            return $voted_val;
		}

		public static function wpvc_check_if_voted_date($pid=null,$termid=null,$from=null,$to=null,$where=null){
			global $wpdb;

			if($pid !=null && $termid != null && $from==null && $to==null)
				$new_sql = "SELECT date  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."' && termid='".$termid."'  ORDER BY id DESC";
			elseif($pid!=null && $termid == null && $from==null && $to==null)
				$new_sql = "SELECT date  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."'  ORDER BY id DESC";
			elseif($pid==null && $termid != null && $from==null && $to==null)
				$new_sql = "SELECT date FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && termid ='".$termid."' ORDER BY id DESC";
			elseif($pid==null && $termid != null && $from!=null && $to!=null)	
				$new_sql = "SELECT date FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && date between '$from' AND '$to' && termid ='".$termid."' ORDER BY id DESC";
			elseif($pid!=null && $termid != null && $from!=null && $to!=null)
				$new_sql = "SELECT date  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && date between '$from' AND '$to' && post_id ='".$pid."' && termid='".$termid."'  ORDER BY id DESC";

            $voted_val = $wpdb->get_results($new_sql,ARRAY_A);
            $num_rows = $wpdb->num_rows;
            return $num_rows;
			
		}

        public static function wpvc_check_buyvote($votesetting,$termid,$post_index,$check_count, $post_count, $freq_count = null){

            //Buyvotes Check
            $buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
            if($buy_votes_status == 'on'){
                $nfree_votes = (int) get_term_meta($termid, 'wpvc_category_nfree_votes', true);            
                if($check_count >= $nfree_votes || $post_count >= $nfree_votes){
                    $response = array('update_vote_count'=>array(
                        'msg'=>'already',
                        'post_index'=> $post_index,
                        'buy_vote' => true,
                        'voting_count'=>$check_count,
                    ));
                    return $response;
                }
            }
            return false;
        }

        public static function wpvc_check_if_voted_buyvote_no_limit($pid=null,$termid=null,$where=null){
			global $wpdb;

			if($pid !=null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."' && termid='".$termid."'";
			elseif($pid!=null && $termid == null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && post_id ='".$pid."'";
			elseif($pid==null && $termid != null)
				$new_sql = "SELECT id  FROM " . WPVC_VOTES_TBL ." WHERE ".$where." && termid ='".$termid."'";

            $voted_val = $wpdb->get_results($new_sql,ARRAY_A);
            $num_rows = $wpdb->num_rows;
            return $num_rows;
			
		}

        private static function get_track_method_where($pid,$termid,$votesetting,$email){
            //Addded for tracking method
            $tracking_method = $votesetting['vote_tracking_method'];
            switch($tracking_method){
                case 'cookie_traced':
                    $useragent = Wpvc_Voting_Model::wpvc_cookie_voting_getBrowser();
                    $voter_cookie = $useragent['name'].'@'.$termid;
                    $ip_addr = Wpvc_Voting_Model::wpvc_getUserIpAddr();
                    $where = "ip ='".$voter_cookie."' && ip_always='".$ip_addr."'";
                break;

                case 'email_verify':
                    if($votesetting['user_logged'] == true){
                        $current_user = get_user_by('id', $votesetting['user_id_profile'] == null ? $GLOBALS['wpvc_user_id'] :$votesetting['user_id_profile'] );
                        $get_email = $current_user->user_email;
                    }
                    else{
                        $get_email = $email;
                        if($get_email == null){
                            $current_user = get_user_by('id',$GLOBALS['wpvc_user_id'] );
                            $get_email = $current_user->user_email;
                        }
                    }
                    $where = "ip ='".$get_email."'";
                break;

                case 'ip_email': 
                    if($votesetting['user_logged'] == true){
                        $current_user = get_user_by('id', $votesetting['user_id_profile'] == null ? $GLOBALS['wpvc_user_id'] :$votesetting['user_id_profile'] );
                        $get_email = $current_user->user_email;
                    }
                    else{ 
                        $get_email = $email;
                        if($get_email == null){
                            $current_user = get_user_by('id',$GLOBALS['wpvc_user_id'] );
                            $get_email = $current_user->user_email;
                        }
                    }
                    $ip_addr = Wpvc_Voting_Model::wpvc_getUserIpAddr();
                    $where = "ip ='".$get_email."' && ip_always='".$ip_addr."'";
                break;

                case 'ip_traced':
                    $ip_addr = Wpvc_Voting_Model::wpvc_getUserIpAddr();
                    $where = "ip ='".$ip_addr."'";
                break;
            }
            return $where;
        }

		public static function wpvc_no_limit_votes($post_id,$termid,$votesetting,$post_index=null,$email= null){
            
			$freq_count = $votesetting['vote_frequency_count'];
			$frequency =  $votesetting['frequency'];
			$voting_type = $votesetting['vote_votingtype'];
			$hourscalc = $votesetting['vote_frequency_hours'];
            
			//Frequency count 
			if($freq_count < 1){
				$freq_count = 1;
			}

            $where = SELF::get_track_method_where($post_id,$termid,$votesetting,$email);

            $check_count = SELF::wpvc_check_if_voted_no_limit(null,$termid,$where);
            $check_pid = SELF::wpvc_check_if_voted_no_limit($post_id,$termid,$where);

            //Buyvotes Check
            $buy_vote = get_option('_wpvc_buyvotes_extension') == 1 ? true : false;
            if($buy_vote){
                $check_count = SELF::wpvc_check_if_voted_buyvote_no_limit(null,$termid,$where);
                $check_pid = SELF::wpvc_check_if_voted_buyvote_no_limit($post_id,$termid,$where);

                $response = SELF::wpvc_check_buyvote($votesetting,$termid,$post_index, $check_count, $check_pid);
                if($response){
                    return $response;
                }

                $buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
                $buy_vote = false;
                if($buy_votes_status == 'on'){
                    $nfree_votes = (int) get_term_meta($termid, 'wpvc_category_nfree_votes', true);
                    $buy_vote = $check_count >= $nfree_votes ? true : false;
                }
            }

			if($freq_count == 1){
				//No Limit Single Contestant: Allows unlimited voting for a single contestant. You can literally keep clicking vote.
				if($voting_type==0){
					//Check no votes casted for term
					if(empty($check_count)){
						return true;
					}else{
						//No Limit Multiple Contestants (exclusive): Allows unlimited voting for all contestants. You can literally keep clicking vote.
						if(!empty($check_pid)){
							return true;
						}
						else{
							$response = array('update_vote_count'=>array(
                                'msg'=>'already',
                                'post_index'=>$post_index,
                                'buy_vote' => $buy_vote
                            ));
							return $response;
						}
					}
				}else{
					return true;
				}
			}else{
				//Frequency Count is more than 1
				//Single Contestant: Allows unlimited voting by 2’s for a single contestant. You can literally keep clicking vote.
				if($voting_type==0){
					//Check no votes casted for term
					if(empty($check_count)){
						return true;
					}else{
						//Multiple Contestants (exclusive): Allows unlimited voting by 2’s for all contestants. You can literally keep clicking vote
						if(!empty($check_pid)){
							return true;
						}
						else{
							$response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                            'buy_vote' => $buy_vote));
							return $response;
						}
					}
				}else{
					return true;
				}
			}
		}

        public static function wpvc_per_calendar_votes($post_id,$termid,$votesetting,$post_index=null,$email= null){
            
			$freq_count = $votesetting['vote_frequency_count'];
			$frequency =  $votesetting['frequency'];
			$voting_type = $votesetting['vote_votingtype'];
			$hourscalc = $votesetting['vote_frequency_hours'];

			//Frequency count 
			if($freq_count < 1){
				$freq_count = 1;
			}
            $where = SELF::get_track_method_where($post_id,$termid,$votesetting,$email);
            $from_date = date("Y-m-d 00:00:01");
            $to_date = date("Y-m-d 23:59:59");
            $check_count = SELF::wpvc_check_if_voted_date(null,$termid,$from_date,$to_date,$where);
            $post_count = SELF::wpvc_check_if_voted_date($post_id,$termid,$from_date,$to_date,$where);

            //Buyvotes Check
            $buy_vote = get_option('_wpvc_buyvotes_extension') == 1 ? true : false;
            if($buy_vote){
                $response = SELF::wpvc_check_buyvote($votesetting,$termid,$post_index, $check_count, $post_count, $freq_count);
                if($response){
                    return $response;
                }

                $buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
                $buy_vote = false;
                if($buy_votes_status == 'on'){
                    $nfree_votes = (int) get_term_meta($termid, 'wpvc_category_nfree_votes', true);
                    $buy_vote = $check_count >= $nfree_votes ? true : false;
                }
            }

            if($freq_count == 1){
                if($voting_type==0){
                    //Single Contestant: Allows the voter to cast 1 vote per calendar day for a single contestant. Voter may vote for a different contestant the next calendar day.
                    if($check_count == 0){
                        return true;
                    }else{
                        $response = array('update_vote_count'=>array(
                                            'msg'=>'already',
                                            'post_index'=>$post_index,
                                            'poptitle'=>'vote_limit_reached',
                                            'popcontent'=>'pls_vote_tomorrow',
                                            'buy_vote' => $buy_vote
                                        ));
                        return $response;
                    }
                }else{
                    //Multiple Contestants (exclusive): Allows the voter to cast 1 vote per calendar day for each contestant. Cannot vote for the same contestant twice in one day.
                    if($post_count==0){
                        return true;
                    }else{
                        if($voting_type==1){
                             $response = array('update_vote_count'=>array(
                                 'msg'=>'already',
                                 'post_index'=>$post_index,
                                 'poptitle'=>'vote_limit_reached',
                                 'popcontent'=>'pls_vote_tomo_for_cal',
                                 'buy_vote' => $buy_vote,
                                 'freq_count'=>$freq_count
                                ));
                        }else{
                            $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,'poptitle'=>'vote_limit_reached','popcontent'=>'pls_vote_tomo_for_cal_split',
                            'buy_vote' => $buy_vote,
                            'freq_count'=>$freq_count));
                        }

                        return $response;
                    }
                }
            }else{
                //Single Contestant: Allows the voter to cast 2 votes per calendar day for a single contestant. Voter may vote for a different contestant up to 2 times the next calendar day.
                if($voting_type==0){
                    if($check_count == 0){
                        return true;
                    }else{
                        if(($post_count!=0) && ($post_count < $freq_count)){
                            return true;
                        }else{
                            $response = array('update_vote_count'=>array(
                                'msg'=>'already',
                                'post_index'=>$post_index,
                                'poptitle'=>'vote_limit_reached',
                                'popcontent'=>'pls_vote_tomorrow',
                                'buy_vote' => $buy_vote,
                                'freq_count'=>$freq_count
                            ));
                            return $response;
                        }
                    }
                }elseif($voting_type==1){ 
                    //Vote for Multiple Contestants (exclusive): Allows the voter to cast 2 votes per calendar day, 1 vote per contestant up to 2 votes. Cannot vote for the same contestant twice in a calendar day.
                    if(($post_count==0) && ($check_count < $freq_count)){
                        return true;
                    }
                    else{
                        $response = array('update_vote_count'=>array(
                            'msg'=>'already',
                            'post_index'=>$post_index,
                            'poptitle'=>'vote_limit_reached',
                            'popcontent'=>'pls_vote_tomorrow',
                            'buy_vote' => $buy_vote,
                            'post_count'=>$check_count,
                            'freq_count'=>$freq_count
                        ));
                        return $response;
                    }
                }else{
                    //Multiple Contestants (split): Allows the voter to cast 2 votes every calendar day in any combination, 2 votes for the same contestant or 1 vote for 2 contestants.
                    if($check_count == 0){
                        return true;
                    }else{
                        if(($post_count!=0) && ($check_count < $freq_count)){
                            return true;
                        }elseif(($post_count==0) && ($check_count < $freq_count)){
                            return true;
                        }else{
                            $response = array('update_vote_count'=>array(
                                'msg'=>'already',
                                'post_index'=>$post_index,
                                'poptitle'=>'vote_limit_reached',
                                'popcontent'=>'pls_vote_tomorrow',
                                'buy_vote' => $buy_vote,
                                'post_count'=>$check_count,
                                'freq_count'=>$freq_count
                            ));
                            return $response;
                        }
                    }
                }
            
            }

        }

        public static function wpvc_every_hour_votes($post_id,$termid,$votesetting,$post_index=null,$email= null){
            
			$freq_count = $votesetting['vote_frequency_count'];
			$frequency =  $votesetting['frequency'];
			$voting_type = $votesetting['vote_votingtype'];
			$hourscalc = $votesetting['vote_frequency_hours'];

			//Frequency count 
			if($freq_count < 1){
				$freq_count = 1;
			}

            $where = SELF::get_track_method_where($post_id,$termid,$votesetting,$email);
            $now = new DateTime(); //current date/time
            $now->modify('-'.$hourscalc.' hours');
            $from_date = $now->format('Y-m-d H:i:s');
            $todate = new DateTime();
            $to_date = $todate->format('Y-m-d H:i:s');
            $get_count_term = SELF::wpvc_check_if_voted_date(null,$termid,$from_date,$to_date,$where);
            $get_count = SELF::wpvc_check_if_voted_date($post_id,$termid,$from_date,$to_date,$where);

            //Buyvotes Check
            $buy_vote = get_option('_wpvc_buyvotes_extension') == 1 ? true : false;
            if($buy_vote){
                $response = SELF::wpvc_check_buyvote($votesetting,$termid,$post_index, $get_count_term, $get_count, $freq_count);
                if($response){
                    return $response;
                }

                $buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
                $buy_vote = false;
                if($buy_votes_status == 'on'){
                    $nfree_votes = (int) get_term_meta($termid, 'wpvc_category_nfree_votes', true);
                    $buy_vote = $check_count >= $nfree_votes ? true : false;
                }
            }

            if($freq_count == 1){
                //Single Contestant: Allows the voter to cast 1 vote per 3 hours for a single contestant. May be different every 3 hours.
                if($voting_type==0){
                    if($get_count_term==0){
                        return true;
                    }else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote));
                        return $response;
                    }
                }else{
                    //Multiple Contestants (exclusive): Allows the voter to cast 1 vote per 3 hours for each contestant. Cannot vote for the same contestant twice within 3 hours.
    
                    if($get_count==0){
                        return true;
                    }
                    else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }
            }else{
                //Single Contestant: Allows the voter to cast 2 votes per 3 hours for only a single contestant. May be different every 3 hours.
                if($voting_type==0){
                    if($get_count_term==0){
                        return true;
                    }else{
                       
                        if($get_count > 0 && $get_count_term < $freq_count){
                            return true;
                        }else{
                            $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                            'buy_vote' => $buy_vote));
                            return $response;
                        }
                    }
                }elseif($voting_type==1){ 
                    //Multiple Contestants (exclusive): Allows the voter to cast 2 votes per 3 hours, 1 per contestant. Cannot vote for the same contestant twice within 3 hours.
                   
                    if(($get_count==0 && $get_count_term < $freq_count)){
                        return true;
                    }
                    else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'post_count'=>$get_count_term,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }
                else{
                    //Multiple Contestants (split): Allows the voter to cast 2 votes every 3 hours in any combination, 2 votes for the same contestant or 1 vote for 2 contestants.
                    if($get_count_term < $freq_count){
                        return true;
                    }
                    else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'post_count'=>$get_count_term,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }
            }
        }

        public static function wpvc_per_category_votes($post_id,$termid,$votesetting,$post_index=null,$email= null){
            
			$freq_count = $votesetting['vote_frequency_count'];
			$frequency =  $votesetting['frequency'];
			$voting_type = $votesetting['vote_votingtype'];
			$hourscalc = $votesetting['vote_frequency_hours'];

			//Frequency count 
			if($freq_count < 1){
				$freq_count = 1;
			}
            $where = SELF::get_track_method_where($post_id,$termid,$votesetting,$email);
            $check_count = SELF::wpvc_check_if_voted_category(null,$termid,$where,'count');
            $get_count = SELF::wpvc_check_if_voted_category($post_id,$termid,$where,'count');

            //Buyvotes Check
            $buy_vote = get_option('_wpvc_buyvotes_extension') == 1 ? true : false;
            if($buy_vote){
                $response = SELF::wpvc_check_buyvote($votesetting,$termid,$post_index, $check_count, $get_count, $freq_count);
                if($response){
                    return $response;
                }

                $buy_votes_status = get_term_meta($termid, 'wpvc_category_buyvotes_settings', true);
                $buy_vote = false;
                if($buy_votes_status == 'on'){
                    $nfree_votes = (int) get_term_meta($termid, 'wpvc_category_nfree_votes', true);
                    $buy_vote = $check_count >= $nfree_votes ? true : false;
                }
            }

            if($freq_count == 1){
                //Single Contestant: Allows the voter to cast 1 vote per category for a single contestant. Once the vote is cast, the voter cannot vote again in this category.
                if($voting_type==0){
                    //Check no votes casted for term
                    if($check_count==0){
                        return true;
                    }else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote));
                        return $response;
                    }
                }else{
                    //Multiple Contestants (exclusive): Allows the voter to cast 1 vote per category for each contestant. The voter cannot vote for the same contestant twice.
                    $check_pid = SELF::wpvc_check_if_voted_category($post_id,$termid,$where);
                    if(empty($check_pid)){
                        return true;
                    }
                    else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }
            }else{
                //Single Contestant: Allows the voter to cast 2 votes per category for a single contestant. Once the both votes are cast, the voter cannot vote again in this category.
                if($voting_type==0){
                    //Check no votes casted for term
                    if($check_count==0){
                        return true;
                    }else{
                        if($get_count > 0 && $check_count < $freq_count){
                            return true;
                        }else{
                            $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                            'buy_vote' => $buy_vote));
                            return $response;
                        }
                    }
                }elseif($voting_type==1){
                    //Multiple Contestants (exclusive): Allows the voter to cast 2 votes per category, 1 vote per contestant up to 2 votes. Once the both votes are cast, the voter cannot vote again in this category.
                    if($get_count == 0 && $check_count < $freq_count){
                        return true;
                    }else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'post_count'=>$check_count,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }else{
                    //Multiple Contestants (split): Allows the voter to cast 2 votes for the entire duration of the contest in any combination, 2 votes for the same contestant or 1 vote for 2 contestants. Once the both votes are cast, the voter cannot vote again in this category.
                    if($check_count < $freq_count){
                        return true;
                    }else{
                        $response = array('update_vote_count'=>array('msg'=>'already','post_index'=>$post_index,
                        'buy_vote' => $buy_vote,
                        'post_count'=>$check_count,
                        'freq_count'=>$freq_count));
                        return $response;
                    }
                }
            }
        }
        
		
	}
}else
die("<h2>".__('Failed to load Voting Save model')."</h2>");

return new Wpvc_Voting_Save_Model();
?>