<?php
 /**
 * Template Name: Video Gallery
 *
 * @version 1.0
 */


get_header();
$querystr = "
    SELECT $wpdb->vimeography_gallery.*
    FROM $wpdb->vimeography_gallery ORDER BY id DESC
";
$galleryInfo = $wpdb->get_results($querystr, OBJECT);
// get the first value, since the first gallery won't always have id = 1
$first = true;
$galleryID = 1;
foreach ( $galleryInfo as $gallery ){
    if ( $first ){
        $first = false;
        $galleryID = $gallery->id;
    }
}
if(isset($_GET['gallery'])){
    $galleryID = $_GET['gallery'];
}
$pageUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

?>

<div class="vimeography-header">
   <h1>Welcome to the members-only video playlists.</h1>
   <p>Enjoy hundreds of hours of presentations and talks from recent Maker Faires. We'll be adding more, so check back often.</p>
   <p>Now viewing:</p>
   <form id="vimeography-galleries" action="<?php echo($pageUrl); ?>" method="get">
      <div class="select">
         <select name="gallery" onchange="this.form.submit();">
            <?php foreach ($galleryInfo as $gallery) { ?>
                <option value="<?php echo($gallery->id); ?>" <?php if($galleryID == $gallery->id){echo("selected");} ?>><?php echo($gallery->title); ?></option>
            <?php } ?>
                <option value="youtube" <?php if($galleryID == 'youtube'){echo("selected");} ?>>Youtube Gallery</option>
         </select>
      </div>
   </form>
</div>

<div class="container page-content video-gallery">
    <?php 
    if($galleryID != 'youtube') {
        echo(do_shortcode('[vimeography id="' . $galleryID . '"]'));
    } else { ?>
        <?php echo do_shortcode('[embedyt]https://www.youtube.com/embed?listType=playlist&amp;list=UUhtY6O8Ahw2cz05PS2GhUbg&amp;layout=gallery[/embedyt]'); ?>
        <h2 class="text-center"><a href="https://www.youtube.com/c/MAKE/featured" target="_blank">See more videos from our Youtube Channel</a></h2>
    <?php } ?>
</div><!-- end .page-content -->

<?php get_footer(); ?>
