<?php

/* * *************************************************** */
/* Determine correct layout                             */
/* * *************************************************** */

function dispLayout($row_layout) {
    $return = '';
    GLOBAL $acf_blocks;
    $activeinactive = ($acf_blocks ? get_field('activeinactive') : get_sub_field('activeinactive'));

    if ($activeinactive == 'Active') {
        switch ($row_layout) {
            case 'buy_tickets_float': //floating buy tickets banner
                $return = getBuyTixPanel($row_layout);
                break;
            case 'three_column': // 3 COLUMN LAYOUT
            case '3_column': // Legacy mf version
                $return = get3ColLayout();
                break;
            case 'six_column': // 6 column navigation panel
            case '6_column': // Legacy mf version
                $return = get6ColLayout();
                break;
            case 'one_column_wysiwyg': // 1 column wysiwyg
            case '1_column_wysiwyg': // Legacy mf version
                $return = get1ColWYSIWYG();
                break;
            case 'one_column': // 1 COLUMN LAYOUT
            case '1_column': // Legacy mf version
                $return = get1ColLayout();
                break;
            case 'call_to_action_panel':  // CTA PANEL
            case 'call_to_action':  // CTA PANEL
                $return = getCTApanel();
                break;
            case 'news_block_panel':  // NEWS BLOCK PANEL
                $return = getNewsBlockpanel();
                break;
            case 'tint_social_block_panel':  // NEWS BLOCK PANEL
                $return = getTintSocialBlockpanel();
                break;
            case 'ribbon_separator_panel':  // Blank Ribbon
                $return = getRibbonSeparatorpanel();
                break;
            case 'static_or_carousel': // IMAGE CAROUSEL (RECTANGLE)
                $return = getImgCarousel();
                break;
            case 'square_image_carousel': // IMAGE CAROUSEL (SQUARE)
                $return = getImgCarouselSquare();
                break;
            case 'panel_rollover_items': // FEATURED Items
                $return = rolloverItems($row_layout);
                break;
            case 'social_media': //social media panel
                $return = getSocialPanel();
                break;
            case 'flag_banner_panel': //flag banner separator
                $return = getFlagBannerPanel();
                break;
            case 'two_column_video': // Video Panels
            case '2_column_video': // Legacy mf version
                $return = getVideoPanel();
                break;
            case 'two_column_image': // Image Panels in the same style as the Video Panels
            case '2_column_image': // Legacy mf version
                $return = getImagePanel();
                break;
            case 'makey_banner': // faire map link separator
                $return = getMakeyBanner();
                break;
            case 'image_slider': // this is gonna end up pretty similar to the image carousel, but we're going to have it as a panel
                $return = getSliderPanel();
                break;
            case 'rss_feed': // pull the rss feed shortcode with user inputs
                $return = getRSSFeed();
                break;
        }
    }
    return $return;
}

/* * *************************************************** */
/*  Function to return 3_column_photo_and_text_panel     */
/* * *************************************************** */

function get3ColLayout() {
    $return = '';

    $return .= '<section class="content-panel three-column">                
                <div class="container">';

    GLOBAL $acf_blocks;
    $panelTitle = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));
    if ($panelTitle) {
        $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title navy-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
    }

    $return .= '   <div class="row">'; //start row
    //get requested data for each column
    $columns = ($acf_blocks ? get_field('column') : get_sub_field('column'));
    foreach ($columns as $column) {
        $return .= '   <div class="col-sm-4">'; //start column
        $data = $column['data'];
        $columnInfo = '';
        switch ($column['column_type']) {
            case 'image':     // Image with optional link
                $alignment = $data['column_list_alignment'];
                $imageArr = $data['column_image_field'];
                $image = '<img alt="' . $imageArr['alt'] . '" class="img-responsive lazyload" src="' . $imageArr['url'] . '" />';

                $cta_link = $data['image_cta'];
                $ctaText = $data['image_cta_text'];

                if (!empty($cta_link)) {
                    $columnInfo = '<a href="' . $cta_link . '">' . $image . '</a>';
                    if (!empty($ctaText)) {
                        $columnInfo .= '<p class="text-' . $alignment . ' sub-caption-dark"><a href="' . $cta_link . '" target="_blank">' . $ctaText . '</a></p>';
                    }
                } else {
                    $columnInfo = $image;
                }
                break;
            case 'paragraph': // Paragraph text
                $columnInfo = '<p>' . $data['column_paragraph'] . '</p>';
                break;
            case 'list':      // List of items with optional links
                $columnInfo = '<div class="flagship-faire-wrp">';
                if (!empty($data['list_title'])) {
                    $columnInfo .= '<p class="line-item list-title">' . $data['list_title'] . '</p>';
                }
                $columnInfo .= '  <ul>';
                foreach ($data['column_list_fields'] as $list_fields) {
                    $list_text = $list_fields['list_text'];
                    $list_link = $list_fields['list_link'];
                    $columnInfo .= '<li>' . (!empty($list_link) ? '<a class="" href="' . $list_link . '">' . $list_text . '</a>' : $list_text) . '</li>';
                }
                $columnInfo .= '  </ul>';
                $columnInfo .= '</div>';
                break;
        }
        $return .= $columnInfo;
        $return .= '</div>'; //end column
    }

    $return .= '</div>'; //end row

    $return .= ' </div>
              </section>'; // end div.container and section.content-panel
    return $return;
}

/* * *************************************************** */
/*  Function to return 6_column_photo_and_text_panel     */
/* * *************************************************** */

function get6ColLayout() {
    $return = '';

    $return .= '<section class="content-panel six-column">';

    GLOBAL $acf_blocks;
    $panelTitle = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));
    if ($panelTitle) {
        $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title navy-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
    }

    $return .= '   <div class="image-grid-row">'; //start row
    //get requested data for each column
    $columns = ($acf_blocks ? get_field('column') : get_sub_field('column'));
    foreach ($columns as $column) {
        $return .= '   <div class="image-grid-col">'; //start column
        $data = $column['data'];

        $imageArr = $data['column_image_field'];
        //var_dump($imageArr['alt']);

        $columnInfo = '';
        //$image = '<img height="" width="" alt="'.$imageArr['alt'].'" class="ximg-responsive" src="' . $imageArr['url'] . '" />';
        //echo $imageArr['url'];

        $imgStyle = 'data-bg="' . $imageArr['url'] . '"';

        $cta_link = $data['image_cta'];
        $ctaText = $data['image_cta_text'];

        $bgColor = $data['button_color'];

        if (!empty($cta_link)) {
            if (!empty($imageArr['url'])) {
                $columnInfo = '<a class="six-col-img lazyload" href="' . $cta_link . '" ' . $imgStyle . '></a>';
            }
            if (!empty($ctaText)) {
                $columnInfo .= '<p class="text-center sub-caption-bottom ' . $bgColor . '"><a href="' . $cta_link . '" target="_blank">' . $ctaText . '</a></p>';
            }
        } else {
            $columnInfo = '<div class="six-col-img lazyload" ' . $imgStyle . '></div>';
        }

        $return .= $columnInfo;
        $return .= '</div>'; //end column
    }

    $return .= '</div>'; //end row

    $return .= ' </section>'; // end div.container and section.content-panel
    return $return;
}

/* Function to return one column wysiwyg */

function get1ColWYSIWYG() {
    $return = '';
    GLOBAL $acf_blocks;
    $column_1 = ($acf_blocks ? get_field('column_1') : get_sub_field('column_1'));
    $cta_button = ($acf_blocks ? get_field('cta_button') : get_sub_field('cta_button'));
    $cta_button_url = ($acf_blocks ? get_field('cta_button_url') : get_sub_field('cta_button_url'));
    $title = ($acf_blocks ? get_field('title') : get_sub_field('title'));
    $return .= '<section class="content-panel single-block">
          <div class="container">';

    if (!empty($title)) {
        $return .= '  <div class="row">
              <div class="col-xs-12 text-center padbottom">
                <h2 class="panel-title grey-underline">' . $title . '</h2>
              </div>
            </div>';
    }

    $return .= '    <div class="row">
              <div class="col-xs-12">' . $column_1 . '</div>
            </div>';

    if (!empty($cta_button)) {
        $return .= '  <div class="row text-center padtop">
              <a class="btn btn-b-ghost" href="' . $cta_button_url . '">' . $cta_button . '</a>
            </div>';
    }

    $return .= '  </div>
          
        </section>';
    return $return;
}

/* * *************************************************** */
/*   Function to return 1_column_photo_and_text_panel  */
/* * *************************************************** */

function get1ColLayout() {

    //loop thru and randomly select an image.
    $hero_array = array();
    if (have_rows('hero_image_repeater')) {
        // loop through the rows of data

        while (have_rows('hero_image_repeater')) {
            the_row();
            // TODO add the URL wrapper
            $hero_image_random = get_sub_field('hero_image_random');
            $hero_image_url = $hero_image_random["url"];

            $image = '<div class="hero-img lazyload" data-bg="' . $hero_image_url . '"></div>';
            $cta_link = get_sub_field('image_cta');

            if (!empty($cta_link)) {
                $columnInfo = '<a href="' . $cta_link . '">' . $image . '</a>';
            } else {
                $columnInfo = $image;
            }
            $hero_array[] = $columnInfo;
        }
        $randKey = array_rand($hero_array, 1);
        $hero_image = $hero_array[$randKey];
    }

    GLOBAL $acf_blocks;
    $hero_text = ($acf_blocks ? get_field('column_title') : get_sub_field('column_title'));
    $cta_button = ($acf_blocks ? get_field('cta_button') : get_sub_field('cta_button'));
    $cta_button_url = ($acf_blocks ? get_field('cta_button_url') : get_sub_field('cta_button_url'));

    //build output
    $return = '';
    $return .= '<section class="hero-panel">';    // create content-panel section

    $return .= '   <div class="row">
                    <div class="col-xs-12">';
    if ($hero_text) {
        $return .= '<div class="top_left"><img src="https://makerfaire.com/wp-content/themes/makerfaire/img/TopLeftCorner.png"></div>'
                . '<div class="panel_title">'
                . '   <div class="panel_text">' . $hero_text . '</div>'
                . '   <div class="bottom_right"><img src="https://makerfaire.com/wp-content/themes/makerfaire/img/BottomRightCorner.png"></div>'
                . '</div>';
    }
    $return .= '        ' . $hero_image .
            '     </div>' .
            '   </div>';

    if (!empty($cta_button)) {
        $return .= ' <div class="row text-center padtop">
                    <a class="btn btn-b-ghost" href="' . $cta_button_url . '">' . $cta_button . '</a>
                  </div>';
    }

    // Because of the aggressive caching on prod, it makes more sense to shuffle the array in javascript
    $return .= '</section><script type="text/javascript">var heroArray = ' . json_encode($hero_array) . ';heroArray.sort(function(a, b){return 0.5 - Math.random()});jQuery(document).ready(function(){jQuery(".hero-img").replaceWith(heroArray[0]);});</script>';
    // this was removed from above function, since the background hero is no longer an image but a background image
    return $return;
}

/* * *************************************************** */
/*   Function to return 2_column_video panel           */
/* * *************************************************** */

function getVideoPanel() {
    //get data submitted on admin page

    $return = '';
    $return .= '<section class="video-panel container-fluid full-width-div">';    // create content-panel section
    //get requested data for each column
    GLOBAL $acf_blocks;
    $video_rows = ($acf_blocks ? get_field('video_row') : get_sub_field('video_row'));
    $videoRowNum = 0;
    foreach ($video_rows as $video) {
        $videoRowNum += 1;
        if ($videoRowNum % 2 != 0) {
            $return .= '<div class="row">';
            $return .= '  <div class="col-sm-4 col-xs-12">
			                <h4>' . $video['video_title'] . '</h4>
								 <p>' . $video['video_text'] . '</p>';
            if ($video['video_button_link']) {
                $return .= '  <a href="' . $video['video_button_link'] . '">' . $video['video_button_text'] . '</a>';
            }
            $return .= '  </div>';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="embed-youtube">
									 <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								  </div>
			              </div>';
            $return .= '</div>';
        } else {
            $return .= '<div class="row">';
            $return .= '  <div class="col-sm-8 col-xs-12">
								  <div class="embed-youtube">
									 <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								  </div>
							  </div>';
            $return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $video['video_title'] . '</h4>
								 <p>' . $video['video_text'] . '</p>';
            if ($video['video_button_link']) {
                $return .= '  <a href="' . $video['video_button_link'] . '">' . $video['video_button_text'] . '</a>';
            }
            $return .= '  </div>';
            $return .= '</div>';
        }
    }
    $return .= '</section>'; // end section/container
    return $return;
}

/* * *************************************************** */
/*   Function to return 2_column_image panel           */
/* * *************************************************** */

function getImagePanel() {
    //get data submitted on admin page

    $return = '';
    $return .= '<section class="image-panel container-fluid full-width-div">';    // create content-panel section
    //get requested data for each column
    GLOBAL $acf_blocks;
    $image_rows = ($acf_blocks ? get_field('image_row') : get_sub_field('image_row'));
    $imageRowNum = 0;
    foreach ($image_rows as $image) {
        $imageRowNum += 1;
        $imageObj = $image['image'];

        if ($imageRowNum % 2 != 0) {
            $return .= '<div class="row ' . $image['background_color'] . '">';
            $return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $image['image_title'] . '</h4>
								 <p>' . $image['image_text'] . '</p>';
            if ($image['image_links']) {
                foreach ($image['image_links'] as $image_link) {
                    $return .= '  	    <a href="' . $image_link['image_link_url'] . '">' . $image_link['image_link_text'] . '</a>';
                }
            }
            $return .= '  </div>';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
            if ($image['image_overlay']['image_overlay_link']) {
                $return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
            }
            $return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
            if ($image['image_overlay']['image_overlay_text']) {
                $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';
                ;
            }
            if ($image['image_overlay']['image_overlay_link']) {
                $return .= '        </a>';
            }
            $return .= '		</div>
			              </div>';
            $return .= '</div>';
        } else {
            $return .= '<div class="row ' . $image['background_color'] . '">';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
            if ($image['image_overlay']['image_overlay_link']) {
                $return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
            }
            $return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
            if ($image['image_overlay']['image_overlay_text']) {
                $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';
            }
            if ($image['image_overlay']['image_overlay_link']) {
                $return .= '        </a>';
            }
            $return .= '  </div>';
            $return .= '</div>';
            $return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $image['image_title'] . '</h4>
								 <p>' . $image['image_text'] . '</p>';
            if ($image['image_links']) {
                foreach ($image['image_links'] as $image_link) {
                    $return .= '  	    <a href="' . $image_link['image_link_url'] . '">' . $image_link['image_link_text'] . '</a>';
                }
            }
            $return .= '  </div>';
            $return .= '</div>';
        }
    }
    $return .= '</section>'; // end section/container
    return $return;
}

/* * *********************************************** */
/* Function to return Buy Tickets Floating Banner */
/* * *********************************************** */

function getBuyTixPanel() {
//gutenburg blocks use get_field, ACF panels use get_sub_field
    GLOBAL $acf_blocks;
    $buy_ticket_url = ($acf_blocks ? get_field('buy_ticket_url') : get_sub_field('buy_ticket_url'));
    $buy_ticket_text = ($acf_blocks ? get_field('buy_ticket_text') : get_sub_field('buy_ticket_text'));

    return '<a href="' . $buy_ticket_url . '" target="_blank"><div class="floatBuyTix">' . $buy_ticket_text . '</div></a>';
}

/* * ****************************************** */
/* Function to return Call to Action panel   */
/* * ****************************************** */

function getCTApanel() {
    GLOBAL $acf_blocks;

    $return = '';
    $cta_title = ($acf_blocks ? get_field('text') : get_sub_field('text'));
    $cta_url = ($acf_blocks ? get_field('url') : get_sub_field('url'));
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $link_target = ($acf_blocks ? get_field('link_target') : get_sub_field('link_target'));

    if (is_array($link_target)) {
        $link_target = $link_target[0];
    }

    $bg_color_class_map = array(
        "Blue" => '',
        "Light Blue" => ' light-blue-ribbon',
        "Red" => ' red-ribbon',
        "Orange" => ' orange-ribbon'
    );
    $return .= '<a href="' . $cta_url . '" target="' . $link_target . '">';
    $return .= '<section class="cta-panel' . $bg_color_class_map[$background_color] . '">';
    $return .= '   <div class="container">
                     <div class="row text-center">
                        <div class="col-xs-12">
                           <h3>                              
                              <span>' . $cta_title . '</span>                              
                           </h3>
                        </div>
                     </div>
                  </div>
               </section></a>';
    return $return;
}

/* * *********************************************** */
/*   Function to build the rollover items panel   */
/* * *********************************************** */

function rolloverItems($row_layout) {
    $return = '';

    GLOBAL $acf_blocks;
    $items_to_show = ($acf_blocks ? get_field('items_to_show') : get_sub_field('items_to_show'));
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $title = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));
    $cta_url = ($acf_blocks ? get_field('cta_url') : get_sub_field('cta_url'));
    $cta_text = ($acf_blocks ? get_field('cta_text') : get_sub_field('cta_text'));


    // Check if the background color selected was white
    $return .= '<section class="featured-item-panel full-width-div ' . $background_color . '"> ';

    if ($title) {
        $return .= '  <div class="panel-title title-w-border-y ' . ($background_color === "white-bg" ? ' navy-underline' : '') . '">
							 <h2>' . $title . '</h2>
						  </div>';
    }

    //build makers array
    $itemArr = array();
    // check if the nested repeater field has rows of data
    if (have_rows('featured_items')) {
        // loop through the rows of data
        while (have_rows('featured_items')) {
            the_row();
            $url = get_sub_field('item_image')['url'];
            $itemArr[] = array('image' => $url,
                'name' => get_sub_field('item_name'),
                'desc' => get_sub_field('item_short_description'),
                'maker_url' => get_sub_field('more_info_url')
            );
        }
    }

    //limit the number returned to $makers_to_show
    $itemArr = array_slice($itemArr, 0, $items_to_show);

    $return .= '<div class="featured-image-grid">';

    //loop thru item data and build the table
    foreach ($itemArr as $item) {
		$markup = !empty($item['maker_url']) ? 'a' : 'div';
		$href = !empty($item['maker_url']) ? 'href="' . $item['maker_url'] . '" target="_blank"' : '';
		
        $return .= '<' . $markup . ' ' . $href . ' class="grid-item lazyload" data-bg="' . $item['image'] . '">';

        if (!empty($item['desc'])) {
            
            $newTab = $item;
            $newTab = ($newTab == true ? "target='_blank'" : "target='_self'");
            
            $return .= '<div class="grid-item-desc">
                     <div class="desc-body"><h4>' . $item['name'] . '</h4>
                     <p class="desc">' . $item['desc'] . '</p></div>';
            if (!empty($item['maker_url'])) {
                $return .= '  <p class="btn btn-blue read-more-link">Learn More</p>'; //<a href="' . $maker['maker_url'] . '"></a>
            }
            $return .= ' </div>';
        }
        // the caption section
        $return .= '  <div class="grid-item-title-block">
		                 <h3>' . $item['name'] . '</h3>
                    </div>';
        $return .= '</' . $markup . '>'; //close .grid-item
    }
    $return .= '</div>';  //close 
    //check if we should display a more maker button

    if ($cta_url) {
        if (empty($cta_text)) {
            $cta_text = 'More Items';
        }
        $return .= '<div class="row">
            <div class="col-xs-12 text-center">
              <a class="btn universal-btn-navy more-makers-link" href="' . $cta_url . '">' . $cta_text . '</a>
            </div>
          </div>';
    }
    $return .= '</section>';
    $return .= '<script type="text/javascript">
                    function fitTextToBox(){
                            jQuery(".grid-item").each(function() {
                                var availableHeight = jQuery(this).innerHeight() - 30;
                                     if(jQuery(this).find(".read-more-link").length > 0){
                                             availableHeight = availableHeight - jQuery(this).find(".read-more-link").innerHeight() - 20;
                                     }

                                     jQuery(jQuery(this).find(".desc-body")).css("mask-image", "-webkit-linear-gradient(top, rgba(0,0,0,1) 80%, rgba(0,0,0,0) 100%)");

                                     if( 561 > jQuery(window).width() ) {
                                       jQuery(jQuery(this).find(".desc-body")).css("mask-image", "none");
                                            jQuery(jQuery(this).find(".desc-body")).css("height", "auto");
                                     } else { 
                                            jQuery(jQuery(this).find(".desc-body")).css("height", availableHeight);
                                     }
                             });
                    }
                    jQuery(document).ready(function(){
                            fitTextToBox();
                         });
                         jQuery(window).resize(function(){
                                 fitTextToBox();
                         });
                </script>';
    return $return;
}

/* * *************************************************** */
/* Function to return Ribbon Separator panel            */
/* * *************************************************** */

function getRibbonSeparatorpanel() {
    GLOBAL $acf_blocks;
    $return = '';
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $bg_color_class_map = array(
        "Blue" => '',
        "Light Blue" => ' light-blue-ribbon',
        "Red" => ' red-ribbon',
        "Orange" => ' orange-ribbon'
    );
    $return .= '<section class="ribbon-separator-panel' . $bg_color_class_map[$background_color] . '">';
    $return .= '   <div class="arrow-left-sm"></div>'
            . '   <div class="arrow-right-sm"></div>';
    $return .= '</section>';
    return $return;
}

/* * *************************************************** */
/* Function to return News Block panel                  */
/* * *************************************************** */

function getNewsBlockpanel() {
    GLOBAL $acf_blocks;
    $args = [
        'tag' => ($acf_blocks ? get_field('tag') : get_sub_field('tag')),
        'title' => ($acf_blocks ? get_field('title') : get_sub_field('title')),
        'link' => ($acf_blocks ? get_field('link') : get_sub_field('link'))
    ];
    require_once 'MF-News-Block.php';
    return do_news_block($args);
}

/* * *************************************************** */
/* Function to return Tint Social Block panel           */
/* * *************************************************** */

function getTintSocialBlockpanel() {
    $args = [
        'personalization_id' => ($acf_blocks ? get_field('personalization_id') : get_sub_field('personalization_id')),
        'title' => ($acf_blocks ? get_field('title') : get_sub_field('title')),
        'hashtags' => ($acf_blocks ? get_field('hashtags') : get_sub_field('hashtags'))
    ];
    require_once 'MF-Social-Block.php';
    return do_social_block($args);
}

/* * *************************************************** */
/* Function to return IMAGE CAROUSEL (RECTANGLE)        */
/* * *************************************************** */

function getImgCarousel() {
    $return = '';
    // IMAGE CAROUSEL (RECTANGLE)
    GLOBAL $acf_blocks;
    $width = ($acf_blocks ? get_field('width') : get_sub_field('width'));
    // check if the nested repeater field has rows of data
    if (have_rows('images')) {

        $return .= '<section class="rectangle-image-carousel ';
        if ($width == 'Content Width') {
            $return .= 'container">';
        } else {
            $return .= '">';
        }
        $return .= '<div id="carouselPanel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">';
        $i = 0;

        // loop through the rows of data
        while (have_rows('images')) {
            the_row();

            $text = get_sub_field('text');
            $url = get_sub_field('url');
            $image = get_sub_field('image');

            if ($i == 0) {
                $return .= '
        <div class="item active">';
                if (get_sub_field('url')) {
                    $return .= '<a href="' . $url . '">';
                }
                $return .= '
            <img class="lazyload" src="' . $image['url'] . '" alt="' . $image['alt'] . '" />';
                if (get_sub_field('text')) {
                    $return .= '
              <div class="carousel-caption">
                <h3>' . $text . '</h3>
              </div>';
                }
                if (get_sub_field('url')) {
                    $return .= '</a>';
                }
                $return .= '
        </div>';
            } else {
                $return .= '<div class="item">
          <img class="lazyload" src="' . $image['url'] . '" alt="' . $image['alt'] . '" />
          <div class="carousel-caption">
            <h3>' . $text . '</h3>
          </div>
        </div>';
            }
            $i++;
        }
        $return .= '</div> <!-- close carousel-inner-->';

        if ($i > 1) {
            $return .= '<a class="left carousel-control" href="#carouselPanel" role="button" data-slide="prev">
        <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_left.png" alt="Image Carousel button left" />
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#carouselPanel" role="button" data-slide="next">
        <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_right.png" alt="Image Carousel button right" />
        <span class="sr-only">Next</span>
      </a>';
        }
        $return .= '
          </div> <!-- close carouselPanel-->
        </section>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to return IMAGE CAROUSEL (SQUARE)           */
/* * *************************************************** */

function getImgCarouselSquare() {
    $return = '';
    // IMAGE CAROUSEL (SQUARE)
    GLOBAL $acf_blocks;
    $width = ($acf_blocks ? get_field('width') : get_sub_field('width'));

    if (have_rows('images')) {
        $return .= '<section class="square-image-carousel ' . ($width == 'Content Width' ? 'container nopad' : '') . '">';
        $return .= '<div class="mtm-carousel owl-carousel">';
        while (have_rows('images')) {
            the_row();

            $text = get_sub_field('text');
            $url = get_sub_field('url');
            $image = get_sub_field('image');
            $return .= '<div class="mtm-car-image lazyload" data-bg="' . $image["url"] . '" style="background-repeat: no-repeat; background-position: center center;background-size: cover;"></div>';
        }
        $return .= '
    </div>

    <a id="left-trigger" class="left carousel-control" href="#" role="button" data-slide="prev">
      <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_left.png" alt="Image Carousel button left" />
      <span class="sr-only">' . __('Previous', 'MiniMakerFaire') . '</span>
    </a>
    <a id="right-trigger" class="right carousel-control" href="#" role="button" data-slide="next">
      <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_right.png" alt="Image Carousel button right" />
      <span class="sr-only">' . __('Next', 'MiniMakerFaire') . '</span>
    </a>
    </section>

    <script>
    jQuery( document ).ready(function() {
      // Carousel init
      jQuery(\'.square-image-carousel .mtm-carousel\').owlCarousel({
        center: true,
        autoWidth:true,
        items:2,
        loop:true,
        margin:0,
        nav:true,
        //navContainer:true,
        autoplay:true,
        autoplayHoverPause:true,
        responsive:{
          600:{
            items:3
          }
        }
      });
      // Carousel left right
      jQuery( ".square-image-carousel #right-trigger" ).click(function( event ) {
        event.preventDefault();
        jQuery( ".square-image-carousel .owl-next" ).click();
      });
      jQuery( ".square-image-carousel #left-trigger" ).click(function( event ) {
        event.preventDefault();
        jQuery( ".square-image-carousel .owl-prev" ).click();
      });
    });
    </script>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to return slider panel                      */
/* * *************************************************** */

function getSliderPanel() {
    GLOBAL $acf_blocks;
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $text_position = ($acf_blocks ? get_field('text_position') : get_sub_field('text_position'));
    $slideshow_title = ($acf_blocks ? get_field('slideshow_title') : get_sub_field('slideshow_title'));
    $slideshow_name = ($acf_blocks ? get_field('slideshow_name') : get_sub_field('slideshow_name'));
    $column_number = ($acf_blocks ? get_field('column_number') : get_sub_field('column_number'));
    $slides = ($acf_blocks ? get_field('slide') : get_sub_field('slide'));

    $return = '';
    $return .= '<section class="slider-panel full-width-div container-fluid ' . $background_color . ' position-' . $text_position . '">';
    if (!empty($slideshow_title)) {
        $return .= '<div class="slideshow-title"><h2>' . $slideshow_title . '</h2></div>';
    }
    $return .= '   <div class="' . $slideshow_name . '-carousel owl-carousel columns-' . $column_number . '">';
    //get requested data for each column
    foreach ($slides as $slide) {
        $imageObj = $slide['image'];
        if (empty($slide['slide_button_text']) && !empty($slide['slide_link'])) {
            $return .= '<a href="' . $slide['slide_link'] . '">';
        }
        $return .= '     <div class="item slide">
		                   <div class="slide-image-section lazyload" data-bg="' . $imageObj['url'] . '">';
        if (!empty($slide['slide_title']) && $column_number > 1) {
            $return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
        }
        if (!empty($slide['slide_button_text']) && $column_number > 1) {
            if (!empty($slide['slide_link'])) {
                $return .= '      <a href="' . $slide['slide_link'] . '">';
            }
            $return .= '          <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
            if (!empty($slide['slide_link'])) {
                $return .= '      </a>';
            }
        }
        // This section is only for one column slideshows that have description text
        if ($column_number == 1) {
            $return .= '    </div>
			                <div class="slide-info-section">';
            if (!empty($slide['slide_title'])) {
                $return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
            }
            if (!empty($slide['slide_text'])) {
                $return .= '     <p class="slide-text">' . $slide['slide_text'] . '</p>';
            }
            if (!empty($slide['slide_button_text'])) {
                if (!empty($slide['slide_link'])) {
                    $return .= '   <a href="' . $slide['slide_link'] . '">';
                }
                $return .= '         <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
                if (!empty($slide['slide_link'])) {
                    $return .= '   </a>';
                }
            }
        }
        $return .= '       </div>
		                 </div>';
        if (!empty($slide['slide_link']) && empty($slide['slide_button_text'])) {
            $return .= '</a>';
        }
    }
    $tabletSlides = 1;
    if ($column_number > 1) {
        $tabletSlides = 2;
    }
    $return .= '   </div>
	            </section>
					
					<script type="text/javascript">
					   jQuery(document).ready(function() {
					   	// slideshow carousel
							jQuery(".' . $slideshow_name . '-carousel.owl-carousel").owlCarousel({
							  loop: true,
							  margin: 15,
							  nav: true,
							  navText: [
								 "<i class=\'fa fa-caret-left\'></i>",
								 "<i class=\'fa fa-caret-right\'></i>"
							  ],
							  autoplay: true,
							  autoplayHoverPause: true,
							  responsive: {
								 0: {
									items: 1
								 },
								 600: { 
								   items: ' . $tabletSlides . '
								 },
								 1000: {
									items: ' . $column_number . '
								 }
							  }
							})
						});
					</script>
					';
    return $return;
}

/* * ************************************************ */
/*  Function to return Social Media Panel           */
/* * ************************************************ */

function getSocialPanel() {
    $return = '';
    GLOBAL $acf_blocks;
    $panel_title = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));
    if (have_rows('active_feeds')) {
        $return .= '
    <section class="social-feeds-panel full-width-div">
      <div class="container">';
        if ($panel_title != '') {
            $return .= '
          <div class="row">
            <div class="col-xs-12 text-center">
              <div class="title-w-border-r">
                <h2>' . $panel_title . '</h2>
              </div>
            </div>
          </div>';
        }
        $return .= '
        <div class="social-row">';
        while (have_rows('active_feeds')) {
            the_row();

            if (get_row_layout() == 'facebook') {
                $facebook_title = get_sub_field('fb_title');
                $facebook_url = get_sub_field('facebook_url');
                $facebook_url_2 = rawurlencode($facebook_url);
                $return .= '
              <div class="social-panel-fb social-panel-feed">
                <h5>' . $facebook_title . '</h5>
                <iframe src="https://www.facebook.com/plugins/page.php?href=' . $facebook_url_2 . '&tabs=timeline&height=468&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
              </div>';
            } elseif (get_row_layout() == 'twitter') {
                $twitter_title = get_sub_field('tw_title');
                $twitter_id = get_sub_field('twitter_id');
                $return .= '
              <div class="social-panel-tw social-panel-feed">
                <div class="twitter-feed-parent">
                  <h5>' . $twitter_title . '</h5>
                  <script type="text/javascript" src="' . get_bloginfo('template_directory') . '/js/twitterFetcher.min.js"></script>
                  <h4>Tweets <span>by <a href="https://twitter.com/' . $twitter_id . '" target="_blank">@' . $twitter_id . '</a></span></h4>
                  <hr />
                  <div id="twitter-feed-body"></div>
                  <script>
                    var twitter_handle = "' . $twitter_id . '";
                    var configProfile = {
                      "profile": {"screenName": twitter_handle},
                      "domId": "twitter-feed-body",
                      "maxTweets": 10,
                      "enableLinks": true,
                      "showUser": true,
                      "showTime": true,
                      "showImages": true,
                      "lang": "en"
                    };
                    twitterFetcher.fetch(configProfile);
                  </script>
                </div>
              </div>';
            } elseif (get_row_layout() == 'instagram') {
                $instagram_title = get_sub_field('ig_title');
                $instagram_iframe = get_sub_field('instagram_iframe');
                $return .= '
              <div class="social-panel-ig social-panel-feed">
                <h5>' . $instagram_title . '</h5>
                ' . $instagram_iframe . '
              </div>';
            }
        }
        $return .= '
        </div>
      </div>
    </section>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to return flag banner panel                 */
/* * *************************************************** */

function getFlagBannerPanel() {
    return '<div class="flag-banner"></div>';
}

/* * *************************************************** */
/* Function to return a banner featuring Makey          */
/* * *************************************************** */

function getMakeyBanner() {
    GLOBAL $acf_blocks;
    $title = ($acf_blocks ? get_field('title_link_text') : get_sub_field('title_link_text'));
    $URL = ($acf_blocks ? get_field('link_url') : get_sub_field('link_url'));
    $background_color = ($acf_blocks ? get_field('background-color') : get_sub_field('background-color'));

    $content = '<div class="makey-banner full-width-div ' . $background_color . '">';
    $content .= '   <div class="container">';
    $content .= '      <div class="picture-holder">';
    $content .= '         <img alt="Maker Robot" height="74" class="lazyload" src="' . get_bloginfo('template_directory') . '/img/maker-robot.png" width="53">';
    $content .= '      </div>';
    $content .= '      <a href="' . $URL . '">' . $title . ' <i class="icon-arrow-right"></i></a>';
    $content .= '   </div>';
    $content .= '</div>';

    return $content;
}

/* * *************************************************** */
/* Function to return the rss feed from user input      */
/* * *************************************************** */

function getRSSFeed() {
    GLOBAL $acf_blocks;
    $title = ($acf_blocks ? get_field('title') : get_sub_field('title'));
    $feed_url = ($acf_blocks ? get_field('feed_url') : get_sub_field('feed_url'));
    $more_link = ($acf_blocks ? get_field('more_link') : get_sub_field('more_link'));
    $number = ($acf_blocks ? get_field('number') : get_sub_field('number'));

    $rss_shortcode = '[make_rss title=' . urlencode($title) . ', feed=' . $feed_url . ', moreLink=' . $more_link . ', number=' . $number . ']';
    echo do_shortcode($rss_shortcode);
}
