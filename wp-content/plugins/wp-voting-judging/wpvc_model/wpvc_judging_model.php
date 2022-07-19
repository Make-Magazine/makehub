<?php 
if(!class_exists('Wpvc_Judging_Model')){
    class Wpvc_Judging_Model {

        //Insert Judging Data
        public static function wpvc_insert_judging_data($answers,$post_id,$termid,$user_id,$avg_score){
            global $wpdb;   
            $ip = Wpvc_Voting_Model::wpvc_getUserIpAddr();
            $data = array('ip' => $ip, 'answers' => $answers,'post_id' => $post_id,'termid' => $termid,'user_id' => $user_id,'avg_score' => $avg_score);
            $format = array('%s','%s','%d','%d','%d','%f');
            $wpdb->insert(WPVC_JUDGES_TBL,$data,$format);
            return $wpdb->insert_id;
        }

        //Select Judging Data based upon $post_id,$termid,$user_id
        public static function wpvc_select_judging_data($post_id,$termid,$user_id){
            global $wpdb;   
            $judgingdata = $wpdb->get_row( $wpdb->prepare( "SELECT judging.*,users.display_name as judge,posts.post_title FROM ".WPVC_JUDGES_TBL." as judging LEFT JOIN ".$wpdb->prefix."users as users ON judging.user_id = users.ID LEFT JOIN ".$wpdb->prefix."posts  as posts ON judging.post_id = posts.ID WHERE post_id = %d AND termid = %d AND user_id = %d  "  , $post_id,$termid,$user_id ),'ARRAY_A' );
            return $judgingdata;
        }

        //Get all Count of Judged Logs Data
        public static function wpvc_select_all_judging_count($term = 0,$judge = 0,$title=0){
            global $wpdb;               
            $where = Wpvc_Judging_Model::getWhereforJudgingAnswer(
                        array('terms.term_id' => $term,'users.ID' =>$judge ,'posts.post_title' => $title)
                    );
            $sql = "SELECT COUNT(judging.id) as count  FROM ".WPVC_JUDGES_TBL." as judging LEFT JOIN ".$wpdb->prefix."posts  as posts ON judging.post_id = posts.ID LEFT JOIN ".$wpdb->prefix."terms  as terms ON judging.termid = terms.term_id  LEFT JOIN ".$wpdb->prefix."users as users ON judging.user_id = users.ID ".$where." ORDER BY judging.id DESC";
            $judgingcount = $wpdb->get_var( $wpdb->prepare( $sql));
            return $judgingcount;
        }

        public static function getWhereforJudgingAnswer($condition = null){
            $where = " WHERE ";
            $and = "";
            $condition =  array_filter($condition, function ($val) {  if (is_string($val)) {return true;}  if ($val > 0) {return true;} else {return false;}});            
           
            if(count($condition) > 1)
                $and = " AND"; 
                       
            foreach($condition as $key => $value){
                if($key === 'posts.post_title'){
                    $where.= " ".$key . " LIKE '%". $value."%' ";
                }
                else{
                    $where.= " ".$key . " = ". $value." ";
                }
                
                if($and != ""){
                    $where.= $and;
                }
            }

            if(count($condition) == 0)
                $where.= " 1 = 1";                        
               
            $check_last_and =  preg_replace('/.*\s/', '', $where);

            if($check_last_and == "AND")
                $where = chop($where,$check_last_and);
        
            return $where;

        }

        //Select All Judging Data for admin 
        public static function wpvc_select_all_judging_data($paged,$term = 0,$judge = 0,$title=0){
            global $wpdb;   
            if($paged==0){
                $offset = 0;
            }else{
                $offset = $paged * 10;
            }
            $where = Wpvc_Judging_Model::getWhereforJudgingAnswer(
                        array('terms.term_id' => $term,'users.ID' =>$judge ,'posts.post_title' => $title)
                    );

            $sql = "SELECT judging.*,posts.post_title,terms.name as term_name,users.display_name as judge  FROM ".WPVC_JUDGES_TBL." as judging LEFT JOIN ".$wpdb->prefix."posts  as posts ON judging.post_id = posts.ID LEFT JOIN ".$wpdb->prefix."terms  as terms ON judging.termid = terms.term_id  LEFT JOIN ".$wpdb->prefix."users as users ON judging.user_id = users.ID ".$where." ORDER BY judging.id DESC LIMIT ".$offset.",10";
            
            $judgingdata = $wpdb->get_results( $wpdb->prepare($sql),'ARRAY_A' );
            return $judgingdata;
        }

        //Select Data for Charts -  Top 10 Contestants by termid
        public static function wpvc_select_top10_judges($termid = null){
            global $wpdb;   
            $judgingdata = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(judging.avg_score) as total_score,judging.post_id,posts.post_title  FROM ".WPVC_JUDGES_TBL." as judging LEFT JOIN ".$wpdb->prefix."posts  as posts ON judging.post_id = posts.ID  WHERE termid = ".$termid." GROUP BY judging.post_id ORDER BY total_score DESC LIMIT 10"),'ARRAY_A' );
            return $judgingdata;
        }

        //Delete Judging Data by Terms
        public static function wpvc_delete_judging_data($termid = null){
            global $wpdb;              
            return $wpdb->query( "DELETE FROM ".WPVC_JUDGES_TBL." WHERE termid = $termid" );
        }

        //Delete Judging Data by Row ID
        public static function wpvc_delete_judging_single_data($row_id){
            global $wpdb;              
            return $wpdb->query( "DELETE FROM ".WPVC_JUDGES_TBL." WHERE id = $row_id" );
        }

        //Delete Multiple Judging Data by Row IDs
        public static function wpvc_delete_judging_multiple_data($row_ids){
            global $wpdb;  
            return $wpdb->query( "DELETE FROM ".WPVC_JUDGES_TBL." WHERE id IN($row_ids)" );    
        }
        
        //GET users using the custom query
        public static function wpvc_get_judge_user(){
            global $wpdb;
            $sql = "SELECT wp_users.ID, wp_users.display_name FROM wp_users  INNER JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id 
                WHERE wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%administrator%'  OR wp_usermeta.meta_value LIKE '%wpvc_judge_role%' ORDER BY wp_users.user_nicename";
            $users = $wpdb->get_results( $wpdb->prepare( $sql));
            return  $users;                
        }

    }
}
else{
    die("<h2>".__('Failed to load Judging model')."</h2>");
}

return new Wpvc_Judging_Model();