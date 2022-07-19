<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_JudgeRest_Controller')){
	class Wpvc_JudgeRest_Controller{

		public function __construct(){			
			add_action( 'rest_api_init', array($this,'wpvc_judge_api_register'));	
			add_action('before_delete_post',array($this,'wpvc_delete_judging_group'));					
		}

		//Delete Judge Entry track 
		public function wpvc_delete_judging_group($post_id){
			$post_type = get_post_type( $post_id );
			$post_status = get_post_status( $post_id );
			if ( $post_type == 'owjudge'){
				
				$judged_terms = get_the_terms( $post_id, WPVC_VOTES_TAXONOMY );
				foreach($judged_terms as $term){
					delete_term_meta($term->term_id,'judging_post_id');
					Wpvc_Judging_Model::wpvc_delete_judging_data($term->term_id);
				}
			}
		}

		public function wpvc_judge_api_register(){				
			register_rest_route('wpvc-voting/v2', '/owdataforjudging/(?P<postid>\d+)', [
				'methods'  => 'GET',
				'callback' => array($this,'ow_api_data_for_judging'),
				'args' => [
					'postid',
				],
				'show_in_index' => FALSE
			]);	

			register_rest_route('wpvc-voting/v2', '/dashboardAnswerdata', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'ow_api_dashboardAnswerdata'),
				'show_in_index' => FALSE
			]);

			register_rest_route('wpvc-voting/v2', '/dashboardChartdata', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'ow_api_dashboardChartdata'),
				'show_in_index' => FALSE
			]);
			
			register_rest_route('wpvc-voting/v2', '/owdataforjudgingInsert', [
				'methods'  => 'POST',
				'callback' => array($this,'ow_api_data_for_judging_insert'),	
				'show_in_index' => FALSE	
			]);

			
			register_rest_route('wpvc-voting/v2', '/frontend_judging', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'frontend_judging_get_data'),
				'show_in_index' => FALSE
			]);

			register_rest_route('wpvc-voting/v2', '/frontend_judging_Insert', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'frontend_judging_data_insert'),
				'show_in_index' => FALSE
			]);


			register_rest_route('wpvc-voting/v2', '/wpvc_resetAnswersByterm', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'judging_reset_answers'),	
				'show_in_index' => FALSE			
			]);

			register_rest_route('wpvc-voting/v2', '/owjudgedashboard', [
				'methods'  => 'GET',
				'callback' => array($this,'judging_dashboard_data'),	
				'show_in_index' => FALSE		
			]);

			register_rest_route('wpvc-voting/v2', '/wpvc_deleteJudgleLog', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'wpvc_deleteJudgleLog'),
				'show_in_index' => FALSE			
			]);

			register_rest_route('wpvc-voting/v2', '/wpvc_deleteJudgleLogMultiple', [
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array($this,'wpvc_deleteJudgleLogMultiple'),
				'show_in_index' => FALSE			
			]);

		}

		//Data Fetching for Judging
		public static function ow_api_data_for_judging($res){
			global $wpdb;
			$judging_id = $res['postid'];	
			$terms = get_terms( array(
				'taxonomy' => WPVC_VOTES_TAXONOMY,
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			));
			$new_terms = array();
			//Add judging_post_id in the REST
			if(!empty($terms)){
				foreach($terms as $term){
					$judging_post_id = get_term_meta($term->term_id,'judging_post_id',true);
					$new_terms[$term->term_id]['term_id'] = $term->term_id;
					$new_terms[$term->term_id]['name'] = $term->name;
					$new_terms[$term->term_id]['judging_post_id'] = $judging_post_id;	

					if($judging_post_id != null){
						$new_terms[$term->term_id]['disabled'] = ($judging_post_id == $judging_id)?false:true;	
					}
					else{
						$new_terms[$term->term_id]['disabled'] = false;
					}
				}	
			}			
			//Get Existing Data
			$judging_data = get_post_meta($judging_id,'judging_data',true);
			//Get Judges from the Users
			/*$judges = get_users( array( 'fields' => array( 'ID', 'display_name' ) , 'role__in' => array('administrator','wpvc_judge_role') ) );*/
			$judges = Wpvc_Judging_Model::wpvc_get_judge_user();
			$results = array(
							'terms' => array_values($new_terms),
							'judges' => $judges
					);
			return new WP_REST_Response(array_merge((array)$results,(array)json_decode($judging_data)),200);
		}


		//Data Insert for Judging
		public static function ow_api_data_for_judging_insert($request_data){	

			$result = $request_data->get_params();				
			
			$terms = get_terms( array(
				'taxonomy' => WPVC_VOTES_TAXONOMY,
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			));

			$judging_id = $result['postId'];
			$title = $result['title'];
			$currentTerms = $result['termValue'];

			foreach($terms as $term){
				if(in_array($term->term_id,$currentTerms)){
					update_term_meta($term->term_id,'judging_post_id',$judging_id);
				}
				else{
					$check_judging_id = get_term_meta($term->term_id,'judging_post_id',true);
					if($check_judging_id != null && $check_judging_id == $judging_id){
						update_term_meta($term->term_id,'judging_post_id',"");
					}
				}
			}
			update_post_meta($judging_id,'judging_data',json_encode($result));

			$data = array(
				'ID' => $judging_id,	
				'post_status'   => 'publish',		
				'post_title'	=>	$title,
			);

			wp_update_post($data);
			
			return true ;
		}

		//Data Fetch in Judging Front End
		public static function frontend_judging_get_data($request_data,$flag = null){
			if($flag == null){
				$param = $request_data->get_params();	
			}
			else{
				$param = $request_data;	
			}			
			$termid = $param['termid'];
			$judge_id = $param['judge_id'];
			$post_id = $param['post_id'];

			$answers = Wpvc_Judging_Model::wpvc_select_judging_data($post_id,$termid,$judge_id);
			$average_score = get_post_meta($post_id,WPVC_JUDGE_SCORE,true);

			$judging_id = get_term_meta($termid,'judging_post_id',true);
			//Get Existing Answer of the Judge
			$results = get_post_meta($judging_id,'judging_data',true);			
			$results = (array)json_decode($results);
			$results['judgingdata'] = $answers;
			$results['average_score'] = $average_score;
			$results['markValue'] = [];			
			return new WP_REST_Response($results,200);
		}

		//Data Insert for Judging Front End
		public static function frontend_judging_data_insert($request_data){
			$result = $request_data->get_params();	 
			$contestant_id = $result['postId'];
			$judge_id = $result['judge_id'];
			$term_id = $result['term_id'];
			$insertData = $result['insertData'];
			$count = 0;
			$avg_score = 0;
			//Calculating The Average Score
			foreach($insertData as $markValue){
				$count += $markValue['value'];
			}
			$avg_score = $count / count($insertData);	

			//Check Already judged or not		
			$answers = Wpvc_Judging_Model::wpvc_select_judging_data($contestant_id,$term_id,$judge_id);		
			if(!empty($answers)){
				if(count($answers) > 0){
					$param = array();
					$param['termid'] = $term_id;
					$param['judge_id'] = $judge_id;
					$param['post_id'] = $contestant_id;	
					$result = Wpvc_JudgeRest_Controller::frontend_judging_get_data($param,1);
					return $result;
				}
			}
			$inserted_id = Wpvc_Judging_Model::wpvc_insert_judging_data(json_encode($insertData),$contestant_id,$term_id,$judge_id,$avg_score); 
			if($inserted_id){	
				
				$param = array();
				$param['termid'] = $term_id;
				$param['judge_id'] = $judge_id;
				$param['post_id'] = $contestant_id;	

				$wpvc_judge_score = get_post_meta($contestant_id,WPVC_JUDGE_SCORE,true);
				$wpvc_judge_score_fl  = floatval($wpvc_judge_score);
				update_post_meta($contestant_id,WPVC_JUDGE_SCORE,$wpvc_judge_score_fl+$avg_score);

				$result = Wpvc_JudgeRest_Controller::frontend_judging_get_data($param,1);
				
				return $result ;
			}
			else
				return false ;
		}

		//Reseting the Judging Answers 
		public static function judging_reset_answers($request_data){			
			$result = $request_data->get_params();	
			$term = $result['term'];	

			$deleted = Wpvc_Judging_Model::wpvc_delete_judging_data($term);		
		
			$args = [
				'post_type' => WPVC_VOTES_TYPE,
				'tax_query' => [
					[
						'taxonomy' => WPVC_VOTES_TAXONOMY,
						'terms' => $term,
						'include_children' => false 
					],
				],
				'numberposts' => -1
			];
			$contestants = get_posts( $args );
			foreach ( $contestants as $post ) : setup_postdata( $post ); 
				delete_post_meta($post->ID,WPVC_JUDGE_SCORE);		
			endforeach; 
			wp_reset_postdata();
			
			return true ;
		}

		//Get judged answer 
		public static function ow_api_dashboardAnswerdata($request_data){
			$res = $request_data->get_params();	
			$paged = $res['paged'];
			$term = $res['term'];
			$judge = $res['judge'];
			$title = $res['title'];
			$answers_count = Wpvc_Judging_Model::wpvc_select_all_judging_count($term,$judge,$title);			
			$answers = Wpvc_Judging_Model::wpvc_select_all_judging_data($paged,$term,$judge,$title);			
			$results = array(				
				'answers' => $answers != null ? $answers : [],				
				'paged_count' => $answers_count != null ? $answers_count : 0,
				'paged'=>$paged
			);
			return new WP_REST_Response($results,200);	
		}

		public static function ow_api_dashboardChartdata($request_data){
			$res = $request_data->get_params();	
			$term = $res['term'];
			$top10 = Wpvc_Judging_Model::wpvc_select_top10_judges($term);

			$final_top10 = $top10;
			//Loop in post Id to get permalink and image
			if(count($top10) > 0){
				$i =0;
				foreach($top10 as $top){
					$final_top10[$i]['link']  = get_permalink($top['post_id']);
					$image  = wp_get_attachment_url(get_post_thumbnail_id($top['post_id']), 'thumbnail');
					$final_top10[$i]['image'] = $image == false ? WPVC_NO_IMAGE_CONTEST : $image;
					$i++;
				}
			}
			$results = array(				
				'top10' => $final_top10,	
			);
			return new WP_REST_Response($results,200);	
		}

		//Delete Judging log Data
		public static function wpvc_deleteJudgleLog($request_data){
			$res = $request_data->get_params();	
			$row_id = $res['row_id'];
			$score =  $res['score'];
			$contestant_id = $res['post_id'];
			$deleted = Wpvc_Judging_Model::wpvc_delete_judging_single_data($row_id);			
			if($deleted){ 
				$wpvc_judge_score = get_post_meta($contestant_id,WPVC_JUDGE_SCORE,true);
				update_post_meta($contestant_id,WPVC_JUDGE_SCORE,$wpvc_judge_score-$score);
				$paged = 0;
				$answers = Wpvc_Judging_Model::wpvc_select_all_judging_data($paged);
				$answers_count = Wpvc_Judging_Model::wpvc_select_all_judging_count();
				$results = array(				
					'answers' => $answers,
					'answers_count' => $answers_count
				);
				return new WP_REST_Response($results,200);		
			}	
		}

		//Deleting Multiple Logs using IDs
		public static function wpvc_deleteJudgleLogMultiple($request_data){
			$res = $request_data->get_params();	
			$rows = $res['rows'];
			$data =  $res['data'];
			
			$deleted = Wpvc_Judging_Model::wpvc_delete_judging_multiple_data(implode(',',$rows));	
			if($deleted){ 
				foreach($data as $answer){
					$score =  $answer['avg_score'];
					$wpvc_judge_score = get_post_meta($answer['post_id'],WPVC_JUDGE_SCORE,true);
					update_post_meta($answer['post_id'],WPVC_JUDGE_SCORE,$wpvc_judge_score-$score);				
				}
				$paged = 0;
				$answers = Wpvc_Judging_Model::wpvc_select_all_judging_data($paged);
				$answers_count = Wpvc_Judging_Model::wpvc_select_all_judging_count();
				$results = array(				
					'answers' => $answers,
					'answers_count' => $answers_count
				);
				return new WP_REST_Response($results,200);
			}		

		}

		//Get Dashboard Data 
		public static function judging_dashboard_data($request_data){
			global $wpdb;

			$terms = get_terms( array(
				'taxonomy' => WPVC_VOTES_TAXONOMY,
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			));
			
			//Get Judges from the Users
		//	$judges = get_users( array( 'fields' => array( 'ID', 'display_name' ) , 'role__in' => array('administrator','wpvc_judge_role') ) );
			$judges = Wpvc_Judging_Model::wpvc_get_judge_user();

			//Assigned Judging TERMS
			$args = array(
				'taxonomy'   => WPVC_VOTES_TAXONOMY,
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'  => 'judging_post_id',						
					)
				)
			);
			$judingterms = get_terms($args);

			if($judingterms) {
				foreach($judingterms as $term){
					$judging_id = get_term_meta($term->term_id,'judging_post_id',true);
					$judge_settings = get_post_meta($judging_id,'judging_data',true);
					$term->judgesettings = $judge_settings;
				}
			}
			//Judged Answers
			$query = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE `meta_key` LIKE 'judging_answers_%'";	
			$answers = $wpdb->get_results($query);	

			$answers_count = Wpvc_Judging_Model::wpvc_select_all_judging_count();
			$paged = 0;
			$answers = Wpvc_Judging_Model::wpvc_select_all_judging_data($paged);
			

			//No. Of Judging Groups
			$group = wp_count_posts('owjudge');
			
			$results = array(
				'terms' => array_values($terms),
				'judges' => $judges,	
				'answers' => $answers,
				'top10'=> [],
				'no_group'  => $group->publish,
				'judingterms' => $judingterms,
				'answers_count' => $answers_count
			);
			return new WP_REST_Response($results,200);			
		}


	}
}
else
die("<h2>".__('Failed to load the Voting Judge Rest Controller','voting-contest')."</h2>");

return new Wpvc_JudgeRest_Controller();
