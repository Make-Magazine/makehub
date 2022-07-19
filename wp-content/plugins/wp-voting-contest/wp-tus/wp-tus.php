<?php
 // Avoid direct calls to this file.
 if ( ! defined( 'ABSPATH' )) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );

    die( 'Access Forbidden' );
}

require __DIR__ . '/vendor/autoload.php';

add_action( 'init', function () {
    add_rewrite_tag( '%tus%', '([^&]+)' );
    add_rewrite_rule( '^wp-tus/([^/]*)/?', 'index.php?tus=$matches[1]', 'top' );
});

 add_action('parse_request', function ( $wp ) { 
    ob_start(); 
    // Return if it is a normal request.
    $query_vars = $wp->query_vars;
    if(empty($query_vars)){
        return;
    }
    if ( $wp->query_vars['name'] !== 'wp-tus' && $wp->query_vars['author_name'] !== 'wp-tus' && $wp->query_vars['category_name'] !== 'wp-tus' && $wp->query_vars['pagename'] !== 'wp-tus' && empty( $wp->query_vars['tus'] ) ) {
        return;
    }

    $server = new \TusPhp\Tus\Server(); // Pass `redis` as first argument if you are using redis.

    $server->setApiPath( '/wp-tus' ) // tus server endpoint.
        ->setUploadDir( wp_get_upload_dir() ['path']);

    $server->event()->addListener('tus-server.upload.created', function (\TusPhp\Events\TusEvent $event) {
        $fileMeta = $event->getFile()->details();
    });

    $server->event()->addListener('tus-server.upload.complete', function (\TusPhp\Events\TusEvent $event) {
        $fileMeta = $event->getFile()->details();
        $request  = $event->getRequest();
        $response = $event->getResponse();        
        
        //Get meta values
        $post_id  = $fileMeta['metadata']['post_id'];
        $system_name  = $fileMeta['metadata']['system_name'];
        $filename = $fileMeta['metadata']['filename'];
        
        $absolutePath = wp_get_upload_dir() ['path'].'/'.$filename;

        $file = $fileMeta['file_path'];

        $wp_filetype = wp_check_filetype($filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit' 
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

              
        if($system_name === 'contestant-ow_video_upload_url'){            
            $value = wp_get_attachment_url( $attach_id );
            update_post_meta($post_id, '_ow_video_upload_attachment', $attach_id);
            update_post_meta($post_id, 'contestant-ow_video_url', $value);
            update_post_meta($post_id, '_ow_video_upload_url', $value);            
        }

        if($system_name === 'contestant-ow_music_url'){
            $value = wp_get_attachment_url( $attach_id );
            update_post_meta($post_id, '_ow_music_upload_attachment', $attach_id);
            update_post_meta($post_id, '_ow_music_upload_url', htmlentities($value,ENT_QUOTES));            
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );        
        $res1   = wp_update_attachment_metadata( $attach_id, $attach_data );
       
        
        if($system_name === 'contestant-image'){
            $res2   = set_post_thumbnail( $post_id, $attach_id );    
        }
        else{           
            $value = wp_get_attachment_url( $attach_id );
            $custom_attachment = array(
                'file'  => $absolutePath,
                'url'   => $value,
                'type'  => $wp_filetype['type'],
                'error' => false,
            );

            update_post_meta($post_id, 'ow_custom_attachment_'.$system_name, $custom_attachment);
        }
        
    });



    $response = $server->serve();
    $response->send();
    exit(0);
});
