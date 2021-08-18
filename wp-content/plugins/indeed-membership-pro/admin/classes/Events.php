<?php
namespace Indeed\Ihc\Admin;

class Events
{
    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        add_action( 'ihc_action_after_delete_membership', [ $this, 'onDeleteMembership' ], 1, 2 );
    }

    /**
     * It will remove the post restrictions for this level.
     * @param int
     * @param bool
     * @return none
     */
    public function onDeleteMembership( $membershipId=null, $processDone=true )
    {
        global $wpdb;
        if ( !$processDone ){
            return;
        }
        $query = "
        SELECT DISTINCT(a.post_id) as ID
        	FROM {$wpdb->postmeta} a
        	INNER JOIN {$wpdb->posts} b
        	ON a.post_id=b.ID
        	INNER JOIN {$wpdb->postmeta} c
        	ON c.post_id=a.post_id
        	WHERE 1=1
        	AND
        	(
        			(
        					( a.meta_key='ihc_mb_type' AND a.meta_value='show' )
        					AND
        					( c.meta_key='ihc_mb_who' AND FIND_IN_SET($membershipId, c.meta_value) )
        			)
        			OR
        			(
        				( a.meta_key='ihc_mb_type' AND a.meta_value='block' )
        				AND
        				( c.meta_key='ihc_mb_who' AND ( FIND_IN_SET($membershipId, c.meta_value)  ) )
        			)
        	)
        ";

        $posts = $wpdb->get_results( $query );

        if ( !$posts ){
            return;
        }

        foreach ( $posts as $postData ){
        		$postSettings = get_post_meta( $postData->ID, 'ihc_mb_who', true );
        		$levelIds = explode( ',', $postSettings );
        		$key = array_search ( $membershipId , $levelIds );
        		if ( $key !== false ){
        				unset( $levelIds[ $key ] );
        		}
        		$levelIds = implode( ',', $levelIds );
        		update_post_meta( $postData->ID, 'ihc_mb_who', $levelIds );
        }
    }

}
