<?php
$element = array(
    'border-radius' => strval(get_option('lds_cg_item_border_radius')),
    'border-size'   => strval(get_option('lds_cg_item_border_size')),
    'background-color' => strval(get_option('lds_cg_item_background')),
    'padding'       => strval(get_option('lds_cg_item_padding')),
    'border-color'  => strval(get_option('lds_cg_item_border_color')),
    'drop-shadow'   => strval(get_option('lds_cg_item_drop_shadow')),
    'hover-effect'  => strval(get_option('lds_cg_hover_effect')),
);

$ribbon = array(
    'style'         =>   strval(get_option('lds_cg_ribbon_style')),
    'text'          => strval(get_option('lds_cg_ribbon_text') ),
    'background'    => strval(get_option('lds_cg_ribbon_background')),
    'text_free'     => strval(get_option('lds_cg_ribbon_free_text')),
    'background_free' => strval(get_option('lds_cg_ribbon_free_background')),
    'text_enrolled' =>  strval(get_option('lds_cg_ribbon_enrolled_text')),
    'background_enrolled'   =>  strval(get_option('lds_cg_ribbon_enrolled_bg')),
);

$button = array(
     'background'   =>   strval( get_option('lds_cg_button_bg') ),
     'text'         =>   strval( get_option('lds_cg_button_txt') )
);

/**
  * Button treatments
  */

if( $button['background'] != '' && !empty($button['background']) ): ?>
     .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary {
          background-color: <?php echo $button['background']; ?>;
          border: 0;
     }
<?php endif;
if( $button['text'] != '' && !empty($button['text']) ): ?>
     .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary {
          color: <?php echo $button['text']; ?>;
     }
<?php endif;

/**
 * Element treatments
 * @var [type]
 */
if( $element['border-radius'] == '0' || !empty($element['border-radius']) ): ?>
    .ld-course-list-items .ld_course_grid .thumbnail.course {
        border-radius: <?php echo $element['border-radius'] . 'px'; ?>;
    }
    .ld-course-list-items .ld_course_grid .thumbnail.course img {
        border-radius: <?php echo $element['border-radius'] . 'px'; ?> <?php echo $element['border-radius'] . 'px'; ?> 0 0;
    }
<?php
endif;

if( $element['background-color'] != '' && !empty($element['background-color']) ): ?>
    .ld-course-list-items .ld_course_grid .thumbnail.course {
        background-color: <?php echo $element['background-color']; ?>;
    }
<?php
endif;


if( $element['border-size'] == '0' || !empty($element['border-size']) ): ?>
    .ld-course-list-items .ld_course_grid .thumbnail.course {
        border-width: <?php echo $element['border-size'] . 'px'; ?>;
    }
<?php
endif;

if( $element['border-color'] !== '' && !empty($element['border-color']) ): ?>
    .ld-course-list-items .ld_course_grid .thumbnail.course {
        border-color: <?php echo $element['border-color']; ?>;
    }
<?php
endif;

if( $element['padding'] !== '' && !empty($element['padding']) ): ?>
     .ld-course-list-items .ld_course_grid article.course div.caption {
          padding: <?php echo $element['padding']; ?>px;
     }
<?php
endif;
if( $element['drop-shadow'] !== '' && !empty($element['drop-shadow']) ): ?>
    .ld-course-list-items .ld_course_grid .thumbnail.course {
        <?php
        switch($element['drop-shadow']):
            case('light'): ?>
                box-shadow: 0 0 15px rgba(0,0,0,0.05);
                <?php
                break;
            case('light-plus'): ?>
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                <?php
                break;
            case('medium'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.05);
                <?php
                break;
            case('medium-plus'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                <?php
                break;
            case('heavy'): ?>
                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                <?php
                break;
            case('heavy-plus'): ?>
                box-shadow: 3px 5px 40px rgba(0,0,0,0.15);
                <?php
                break;
            endswitch; ?>
        }
<?php
endif;

if( $element['hover-effect'] !== '' && !empty($element['hover-effect']) ): ?>

     .ld-course-list-items .ld_course_grid article.course {
          transition: all ease-in-out 250ms;
     }

     <?php
     if( $element['hover-effect'] == 'flip' ): ?>
          .ld-course-list-items .ld_course_grid article.course {
               transform-style: preserve-3d;
               transition: transform: 0.8s;
          }
          .ld-course-list-items .ld_course_grid article.course::after {
               <?php
               if( $button['background'] != '' && !empty($button['background']) ): ?>
                    background: <?php echo $button['background']; ?>;
               <?php else: ?>
                    background: #3276b1;
               <?php endif; ?>
               position: absolute;
               height: 100%;
               width: 100%;
               content: '';
               display: block;
               transform: rotateY(180deg);
               -webkit-backface-visibility: hidden; /* Safari */
               backface-visibility: hidden;
          }
          .ld-course-list-items .ld_course_grid p.ld_course_grid_button {
               position: absolute;
               top: 50%;
               z-index: 10;
               display: none !important;
               width: 100%;
               left: 0;
               right: 0;
               padding-left: 30px;
               padding-right: 30px;
          }
          .ld-course-list-items .ld_course_grid:hover p.ld_course_grid_button {
               transform: translateY(-50%) rotateY(180deg);
               display: block !important;
          }
          .ld-course-list-items .ld_course_grid:hover p.ld_course_grid_button a {
               <?php
               if( $button['background'] != '' && !empty($button['background']) ): ?>
                    color: <?php echo $button['background']; ?> !important;
               <?php else: ?>
                    color: #3276b1 !important;
               <?php endif; ?>
               background: #fff !important;
               border-color: 0;
          }
     <?php endif; ?>

     .ld-course-list-items .ld_course_grid article.course:hover {
          <?php switch( $element['hover-effect'] ):
               case('highlight'): ?>
                    background: #ffffed;
               <?php
               break;
               case('elevate'): ?>
                    transform: translateY(-10px);
               <?php
               break;
               case('flip'): ?>
                    transform: rotateY(-180deg);
               <?php
               break;
               case('reverse'):
                    if( $button['background'] != '' && !empty($button['background']) ): ?>
                         background: <?php echo $button['background']; ?>;
                    <?php else: ?>
                         background: #3276b1 !important;
                    <?php endif;
               break;
          endswitch; ?>
     }
     <?php
     if( $element['hover-effect'] == 'reverse' ): ?>
          .ld-course-list-items .ld_course_grid article.course:hover p.ld_course_grid_button a {
               <?php
               if( $button['text'] != '' && !empty($button['text']) ): ?>
                    background-color: <?php echo $button['text']; ?>;
               <?php else: ?>
                    background-color: #fff;
               <?php endif;
               if( $button['background'] != '' && !empty($button['background']) ): ?>
                    color: <?php echo $button['background']; ?>;
               <?php else: ?>
                    color: #3276b1;
               <?php endif; ?>
          }
          .ld-course-list-items .ld_course_grid article.course:hover p.entry-content,
          .ld-course-list-items .ld_course_grid article.course:hover h3.entry-title {
               <?php
               if( $button['text'] != '' && !empty($button['text']) ): ?>
                    color: <?php echo $button['text']; ?>;
               <?php else: ?>
                    color: #fff;
               <?php endif; ?>
          }

     <?php endif;

endif;

if( $ribbon['text'] !== '' && !empty($ribbon['text']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
    color: <?php echo $ribbon['text']; ?>;
}
<?php endif;

if( $ribbon['background'] !== '' && !empty($ribbon['background']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
    background: <?php echo $ribbon['background']; ?>;
}
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price::before {
    border-top-color: <?php echo ldvc_color_shift( $ribbon['background'], -0.2 ); ?>;
    border-right-color: <?php echo ldvc_color_shift( $ribbon['background'], -0.2 ); ?>;
}
<?php endif;

if( $ribbon['text_free'] !== '' && !empty($ribbon['text_free']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-free {
    color: <?php echo $ribbon['text_free']; ?>;
}
<?php endif;

if( $ribbon['background_free'] !== '' && !empty($ribbon['background_free']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-free {
    background: <?php echo $ribbon['background_free']; ?>;
}
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-free::before {
    border-top-color: <?php echo ldvc_color_shift( $ribbon['background_free'], -0.2 ); ?>;
    border-right-color: <?php echo ldvc_color_shift( $ribbon['background_free'], -0.2 ); ?>;
}
<?php endif;

if( $ribbon['text_enrolled'] !== '' && !empty($ribbon['text_enrolled']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled {
    color: <?php echo $ribbon['text_enrolled']; ?>;
}
<?php endif;

if( $ribbon['background_enrolled'] !== '' && !empty($ribbon['background_enrolled']) ): ?>
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled {
    background: <?php echo $ribbon['background_enrolled']; ?>;
}
.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled::before {
    border-top-color: <?php echo ldvc_color_shift( $ribbon['background_enrolled'], -0.2 ); ?>;
    border-right-color: <?php echo ldvc_color_shift( $ribbon['background_enrolled'], -0.2 ); ?>;
}
<?php endif;

switch( $ribbon['style'] ):
     case('modern'): ?>
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price::before,
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled::before {
               display: none;
          }
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
               border-radius: 2px;
               top: 15px;
               left: 15px;
               box-shadow: none;
               text-shadow: none;
               border: 0;
               padding: 3px 15px;
          }
     <?php
     break;
     case('band'): ?>
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price::before,
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled::before {
               display: none;
          }
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
               top: 0;
               left: 0;
               right: 0;
               border: 0;
               box-shadow: none;
               text-shadow: none;
               padding: 5px 15px;
          }
     <?php
     break;
     case('overlay'): ?>
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price::before,
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled::before {
               display: none;
          }
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
               background: transparent !important;
               box-shadow: none;
               top: 160px;
               text-shadow: 1px 1px 2px rgba(0,0,0,.15);
               left: 15px;
          }
     <?php
     break;
     case('tab'): ?>
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price::before,
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled::before {
               display: none;
          }
          .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
               <?php
               if( isset($element['background-color']) && !empty($element['background-color']) ): ?>
                    background-color: <?php echo $element['background-color']; ?> !important;
               <?php else: ?>
                    background-color: #fff !important;
               <?php endif; ?>
               box-shadow: none;
               top: 160px;
               padding: 5px 15px 0 15px;
               text-shadow: none;
               left: 15px;
          }
     <?php
     break;
endswitch;


/**
 * Dev admin_notices
 *
 * Left off at ribon colors, still need to do hover effects...
 */
