<?php
function ldms_add_attachment( $comment = null ) {

    $comment_id = ( $comment == null ? get_comment_ID() : $comment->comment_ID );

    $attachmentId = get_comment_meta($comment_id, 'attachmentId', TRUE);

    if(is_numeric($attachmentId) && !empty($attachmentId)){

        // atachement info
        $attachmentLink = wp_get_attachment_url($attachmentId);
        $attachmentMeta = wp_get_attachment_metadata($attachmentId);
        $attachmentName = basename(get_attached_file($attachmentId));
        $attachmentType = get_post_mime_type($attachmentId);
        $attachmentRel  = '';

        // let's do wrapper html
        $contentBefore  = '<div class="attachmentFile"><p>';
        $contentAfter   = '</p><div class="clear clearfix"></div></div>';

        // admin behaves differently
        if(is_admin()){
            $contentInner = $attachmentName;
        } else {
            // shall we do image thumbnail or not?
            if( in_array($attachmentType, ldms_get_image_mime_types() )){
                $attachmentRel = 'rel="lightbox"';
                $contentInner = wp_get_attachment_image($attachmentId, ATT_TSIZE);
                // audio player?
            } elseif ( in_array($attachmentType, ldms_get_audio_mime_types()) ){
                if(shortcode_exists('audio')){
                    $contentInner = '[audio src="'.$attachmentLink.'"][/audio]';
                } else {
                    $contentInner = $attachmentName;
                }
                // video player?
            } elseif ( in_array($attachmentType, ldms_get_video_mime_types() ) ){
                if(shortcode_exists('video')){
                    $contentInner .= '[video src="'.$attachmentLink.'"][/video]';
                } else {
                    $contentInner = $attachmentName;
                }
                // rest ..
            } else {
                $contentInner = '&nbsp;<strong>' . $attachmentName . '</strong>';
            }
        }

        // attachment link, if it's not video / audio
        if(is_admin()){
            $contentInnerFinal = '<a '.$attachmentRel.' class="attachmentLink" target="_blank" href="'. $attachmentLink .'" title="Download: '. $attachmentName .'">';
            $contentInnerFinal .= $contentInner;
            $contentInnerFinal .= '</a>';
        } else {
            if( !in_array($attachmentType, ldms_get_audio_mime_types() ) && !in_array($attachmentType, ldms_get_video_mime_types() ) ){
                $contentInnerFinal = '<a '.$attachmentRel.' class="attachmentLink" target="_blank" href="'. $attachmentLink .'" title="Download: '. $attachmentName .'">';
                $contentInnerFinal .= $contentInner;
                $contentInnerFinal .= '</a>';
            } else {
                $contentInnerFinal = $contentInner;
            }
        }

        // bring a sellotape, this needs taping together
        $contentInsert = $contentBefore . $contentInnerFinal . $contentAfter;

        $comment->comment_content = $comment->comment_content . $contentInsert;

    }

    return $comment;

}

function ldms_get_image_mime_types() {

    return apply_filters( 'ldms_image_mime_types', array(
        'image/jpeg',
        'image/jpg',
        'image/jp_',
        'application/jpg',
        'application/x-jpg',
        'image/pjpeg',
        'image/pipeg',
        'image/vnd.swiftview-jpeg',
        'image/x-xbitmap',
        'image/gif',
        'image/x-xbitmap',
        'image/gi_',
        'image/png',
        'application/png',
        'application/x-png'
    ) );

}

function ldms_get_audio_mime_types() {

    return apply_filters( 'ldms_audio_mime_types', array(
        'audio/mpeg',
        'audio/x-mpeg',
        'audio/mp3',
        'audio/x-mp3',
        'audio/mpeg3',
        'audio/x-mpeg3',
        'audio/mpg',
        'audio/x-mpg',
        'audio/x-mpegaudio',
        'audio/mp4a-latm',
        'audio/ogg',
        'application/ogg',
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/x-pn-wav',
        'audio/x-ms-wma'
    ) );

}

function ldms_get_video_mime_types() {
    return apply_filters( 'ldms_video_mime_types', array(
        'video/mp4v-es',
        'audio/mp4',
        'video/mp4',
        'video/x-m4v',
        'video/quicktime',
        'video/x-quicktime',
        'image/mov',
        'audio/aiff',
        'audio/x-midi',
        'audio/x-wav',
        'video/avi',
        'video/x-ms-wmv',
        'video/avi',
        'video/msvideo',
        'video/x-msvideo',
        'image/avi',
        'video/xmpg2',
        'application/x-troff-msvideo',
        'audio/aiff',
        'audio/avi',
        'video/avi',
        'video/mpeg',
        'video/mpg',
        'video/x-mpg',
        'video/mpeg2',
        'application/x-pn-mpg',
        'video/x-mpeg',
        'video/x-mpeg2a',
        'audio/mpeg',
        'audio/x-mpeg',
        'image/mpg',
        'video/ogg',
        'audio/3gpp',
        'video/3gpp',
        'video/3gpp2',
        'audio/3gpp2',
        'video/x-flv',
        'video/webm',
    ) );
}
