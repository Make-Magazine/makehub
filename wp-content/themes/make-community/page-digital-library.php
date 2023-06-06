<?php
/**
 * Template Name: Digital-Library pages
 */
get_header();

while ( have_posts() ) : the_post(); ?>

<section class="wrapper bluetoad">
    <main id="content" class="no-sidebar">
        <article>
            <?php the_content(); ?>
            <iframe id="bluetoad-iframe" src="/wp-content/themes/make-community/blue-toad-login.php"></iframe>        
        </article>
    </main>
</section>
<?php
// End of the loop.
endwhile; ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        var is_iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (is_safari || is_iOS) {
            window.location = "/wp-content/themes/make-community/blue-toad-login.php";
        }
        jQuery(".page-template-page-digital-library footer").mouseleave(function () {
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery(".wrapper.bluetoad iframe").offset().top
            }, 100);
        });
    });
</script>

<?php
get_footer();
