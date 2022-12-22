<?php

add_action( 'add_meta_boxes', 'lds_custom_lesson_and_topic_metaboxes' );
function lds_custom_lesson_and_topic_metaboxes() {

    $post_types = apply_filters( 'lds_custom_meta_box_locations', array(
        'sfwd-lessons',
        'sfwd-courses',
        'sfwd-topic',
        'sfwd-quiz'
    ) );

    foreach( $post_types as $type ) {
        add_meta_box( 'lds_meta', __( 'Content Type / Duration', 'lds_skins' ), 'lds_custom_lesson_and_topics_meta', $type, 'side' );
    }

    add_meta_box( 'lds_sales_page', __( 'Sales Page', 'lds_skins' ), 'lds_custom_sales_page_meta', 'sfwd-courses', 'side' );

}

function lds_custom_sales_page_meta( $post ) {

      wp_nonce_field( basename(__FILE__), 'lds_meta_nonce' );

      $lds_sales_page = get_post_meta( $post->ID, '_lds_sales_page', true ); ?>

      <p>
           <label for="lds_sales_page"><?php esc_html_e( 'Custom sales page', 'lds_skins' ); ?></label>
           <input for="lds_sales_page" type="text" value="<?php echo esc_attr($lds_sales_page); ?>" name="lds_sales_page">
           <label for="lds_sales_page" class="lds-form-help"><?php esc_html_e( 'Custom sales page to redirect users who don\'t have access', 'lds_skins' ); ?></label>
      </p>

      <?php

}

function lds_custom_lesson_and_topics_meta( $post ) {

    wp_nonce_field( basename(__FILE__), 'lds_meta_nonce' );
    $lds_post_meta = get_post_meta( $post->ID ); ?>

    <div class="lds-form-table">

        <?php if( get_post_type() == 'sfwd-courses' ): ?>
            <p>
                <label for="lds_course_icon"><?php esc_html_e( 'Course Icon', 'lds_skins' ); ?></label>
                <input class="form-control icp icp-auto lds-course-icon" name="lds_course_icon" value="<?php if( isset($lds_post_meta['_lds_course_icon']) ) echo $lds_post_meta['_lds_course_icon'][0]; ?>" type="text" />
                <label for="lds_course_icon" class="lds-form-help"><?php esc_html_e( 'For images use the featured image option on this page.', 'lds_skins' ); ?></label>
            </p>
            <?php
            if( ldvc_use_iconpicker() ): ?>
                <script>
                    jQuery(document).ready(function() {
                        jQuery('.lds-course-icon').iconpicker();
                    });
                </script>
        <?php endif;
        endif; ?>

        <p>
            <label for="lds_content_type"><?php esc_html_e('Content Type','lds_skins'); ?></label>
            <select name="lds_content_type" id="lds_content_type">
                <?php
                $lds_content_types = apply_filters( 'lds_content_types', array(
                    'fa-file-text-o'    =>  __( 'Text', 'lds_skins' ),
                    'fa-play'           =>  __( 'Video', 'lds_skins' ),
                    'fa-file-image-o'   =>  __( 'Image', 'lds_skins' ),
                    'fa-headphones'     =>  __( 'Audio', 'lds_skins' ),
                    'fa-tv'             =>  __( 'Presentation', 'lds_skin' ),
                    'fa-pencil-square-o' =>  __( 'Assignment', 'lds_skin' )
                ));
                if ( isset ( $lds_post_meta['_lds_content_type'] ) ): ?>
                    <option value="<?php echo esc_attr( $lds_post_meta['_lds_content_type'][0] ); ?>"><?php echo esc_html($lds_content_types[$lds_post_meta['_lds_content_type'][0]]); ?></option>
                <?php endif; ?>
                <option value=""></option>
                <?php foreach( $lds_content_types as $value => $label ): ?>
                    <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="lds_course_icon"><?php esc_html_e( 'Content Icon', 'lds_skins' ); ?></label>
            <input class="form-control icp icp-auto lds-content-icon" name="lds_course_icon" value="<?php if( isset($lds_post_meta['_lds_course_icon']) ) echo $lds_post_meta['_lds_course_icon'][0]; ?>" type="text" />
            <label for="lds_course_icon" class="lds-form-help"><a href="https://fontawesome.com/icons" target="_new"><?php esc_html_e( 'FontAwesome icon options', 'learndash' ); ?></a></label>
        </p>
        <?php
        if( ldvc_use_iconpicker() ): ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery('.lds-form-table .lds-content-icon').iconpicker();
                });
            </script>
        <?php endif; ?>
        <p>
            <label for="lds_duration"><?php esc_html_e('Estimated Duration','lds_skins'); ?></label>
            <input type="text" name="lds_duration" value="<?php if( isset($lds_post_meta['_lds_duration']) ) echo $lds_post_meta['_lds_duration'][0]; ?>">
            <label for="lds_duration" class="lds-form-help"><?php esc_html_e( 'e.g. 5min, 10min 1hr', 'sfwd-lms' ); ?></label>
        </p>
        <p>
            <label for="lds_short_description"><?php esc_html_e('Short Description','lds_skins'); ?></label>
            <textarea name="lds_short_description" class="large-text"><?php if( isset($lds_post_meta['_lds_short_description']) ) echo $lds_post_meta['_lds_short_description'][0]; ?></textarea>
            <label for="lds_duration" class="lds-form-help"><?php esc_html_e( 'One or two sentances is ideal.', 'sfwd-lms' ); ?></label>
        </p>

        <input name="lds_post_type" type="hidden" value="<?php echo esc_attr( get_post_type() ); ?>">

    </div>

<?php
}

add_action( 'save_post', 'lds_meta_save' );
function lds_meta_save( $post_id ) {

    // Checks save status
   $is_autosave = wp_is_post_autosave( $post_id );
   $is_revision = wp_is_post_revision( $post_id );

   $is_valid_nonce = ( isset( $_POST[ 'lds_meta_nonce' ] ) && wp_verify_nonce( $_POST[ 'lds_meta_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

   // Exits script depending on save status
   if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
       return;
   }

     $post_types = apply_filters( 'lds_meta_supported_post_types', array(
          'sfwd-courses',
          'sfwd-lessons',
          'sfwd-topic',
          'sfwd-quiz'
     ) );

   if( !in_array( get_post_type($post_id), $post_types ) ) {
        return;
   }

   $origin_post_type = $_POST['lds_post_type'];

   if( $origin_post_type !== get_post_type($post_id) ) {
        return false;
   }

   $meta_fields = apply_filters( 'lsd_meta_save_fields', array(
      'lds_duration',
      'lds_short_description',
      'lds_content_type',
      'lds_course_icon'
   ));

   foreach( $meta_fields as $field ) {
       if( isset( $_POST[$field] ) ) update_post_meta( $post_id, '_' . $field, wp_kses_post( $_POST[$field] ) );
   }

}
