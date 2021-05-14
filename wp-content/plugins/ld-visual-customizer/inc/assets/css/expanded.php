<?php
$colors = array(
    'primary'   => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_primary' ),
    'secondary' => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_secondary' ),
    'tertiary'  => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_tertiary' ),
); ?>

<style type="text/css">

    <?php
    if( isset($colors['secondary']) && !empty($colors['secondary']) ): ?>

        .learndash-wrapper.lds-template-expanded .ld-table-list-item-preview.learndash-complete::before,
        .learndash-wrapper.lds-template-expanded .ld-table-list-item-preview.learndash-complete::after, 
        .learndash-wrapper.lds-template-expanded .ld-course-navigation .learndash-complete + .ld-lesson-item-section-heading .ld-lesson-section-heading::after,
        .learndash-wrapper.lds-template-expanded .ld-quiz-complete .ld-icon-quiz,
        .learndash-wrapper.lds-template-expanded .ld-item-list-item.learndash-complete + .ld-item-list-section-heading .ld-lesson-section-heading::before,
        .learndash-wrapper.lds-template-expanded .ld-item-list-section-heading::after,
        .ld-table-list.ld-topic-list .learndash-complete .ld-table-list-item-preview::before,
        .ld-table-list.ld-topic-list .learndash-complete .ld-table-list-item-preview::after,
        .learndash-wrapper.lds-template-expanded .learndash-complete .ld-item-list-item-expanded .ld-table-list-item-preview::before,
        .learndash-wrapper.lds-template-expanded .ld-lesson-item.learndash-complete::after,
        .learndash-wrapper.lds-template-expanded .ld-course-navigation .ld-lesson-item.learndash-complete::after,
        .learndash-wrapper.lds-template-expanded .ld-item-list .ld-item-list-item.learndash-complete::after,
        .learndash-wrapper.lds-template-expanded .ld-lesson-item.learndash-complete::before,
        .learndash-wrapper.lds-template-expanded .ld-item-list .learndash-complete .ld-item-list-item::after {
            background: <?php echo $colors['secondary']; ?> !important;
        }

    <?php
    endif; ?>

</style>

<?php
/*
.learndash-wrapper.lds-template-expanded .ld-item-list-item-expanded .ld-table-list-item.learndash-complete .ld-table-list-item-preview::after,
.learndash-wrapper.lds-template-expanded .ld-item-list-item-expanded .ld-table-list-item.learndash-complete .ld-table-list-item-preview::before,
.learndash-wrapper.lds-template-expanded .ld-item-list-item-expanded .ld-table-list-item .ld-table-list-item-preview.learndash-complete::after,
.learndash-wrapper.lds-template-expanded .ld-item-list-item-expanded .ld-table-list-item .ld-table-list-item-preview.learndash-complete::before,
 */
