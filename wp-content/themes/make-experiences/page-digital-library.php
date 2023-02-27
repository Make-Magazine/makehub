<?php
/**
 * Template Name: Digital-Library pages
 */
get_header();
?>

<div id="page-content" class="mmBack bluetoad">

    <?php
    // theloop
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            ?>
            <iframe id="bluetoad-iframe" src="/wp-content/themes/make-experiences/blue-toad-login.php"></iframe>
                <?php the_content(); ?>
            <?php } // END WHILE ?>
        <?php } else { ?>
            <?php get_404_template(); ?>
<?php } // END IF  ?>

</div><!-- end .page-content -->

<script type="text/javascript">
    jQuery(document).ready(function () {
        var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        var is_iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (is_safari || is_iOS) {
            window.location = "/wp-content/themes/make-experiences/blue-toad-login.php";
        }
        jQuery(".page-template-page-digital-library footer").mouseleave(function () {
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery("#page-content.bluetoad iframe").offset().top
            }, 100);
        });
    });
</script>

<?php
wp_footer();
?>
