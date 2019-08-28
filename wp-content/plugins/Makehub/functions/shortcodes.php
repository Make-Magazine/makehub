<?php

// [make_rss title="Makerspace", feed="https://makezine.com/tag/makerspaces/feed/", moreLink="http://makezine.com/tag/makerspaces/", number=4]
function make_rss_func($atts) {
    $a = shortcode_atts(array(
        'title' => '',
        'feed' => 'https://makezine.com/feed/',
        'moreLink' => "",
        'number' => 6
            ), $atts);
    $return = '    
    <div class="container makerspace-news">
        <h2>' . $a['title'] . ' News from <img class="logo" src="https://make.co/wp-content/themes/memberships/img/make_logo.svg" /> magazine</h2>
        <div class="row posts-feeds-wrapper">';


    $rss = fetch_feed($a['feed']);
    if (!is_wp_error($rss)) {
        $maxitems = $rss->get_item_quantity($a['number']); //gets latest x items, this can be changed to suit your requirements
        $rss_items = $rss->get_items(0, $maxitems);
    }

    if ($maxitems == 0) {
        $return .= '<li>No items.</li>';
    } else {
        foreach ($rss_items as $item) {
            $return .= '
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="post-feed">
                            <a class="full-link" href="' . esc_url($item->get_permalink()) . '" target="_blank">
                                <div class="title">
                                    <p class="p-title">' . esc_html($item->get_title()) . '</p>
                                    <img src="' . get_first_image_url($item->get_content()) . '" alt="' . esc_html($item->get_title()) . ' featured image">                                    
                                    <p>' . shorten(get_summary($item->get_content()), 120) . '</p>
                                </div>
                            </a>
                        </div>
                    </div>';
        }
    }
    if ($a['moreLink'] != '') {
        $return .= '
                <div class="col-xs-12">
                    <a class="btn universal-btn btn-more-articles" href="' . $a['moreLink'] . '" target="_blank">See more articles</a>
                </div>';
    }
    $return .= '    
        </div>

    </div>';
    return $return;
}

add_shortcode('make_rss', 'make_rss_func');
