<?php
/* **************************************************** */
/* Determine correct layout                             */
/* **************************************************** */

function dispLayout($row_layout) {
	error_log($row_layout);
   $return = '';
   $activeinactive = get_sub_field('activeinactive');
   if ($activeinactive == 'Active') {
      switch ($row_layout) {
         case 'banner': // FULL WIDTH BANNER WITH OPTIONS FOR H1, H2, BUTTONS AND BG IMAGES
            $return = banner();            
            break;
         case 'three_column_header_footer': // 3 COLUMN LAYOUT with HEADER and FOOTER
            $return = get3columnsHeaderFooter();            
            break;
			case 'three_column_header': // 3 COLUMN LAYOUT with ONLY HEADER
            $return = get3columnsHeader();            
            break;
			case 'two_column_list': // 2 COLUMN LAYOUT WITH A LIST IN EACH BOx
            $return = get2columnsList();            
            break;
      }
   }
   return $return;
}

/***************************************************************************/
/*   Function to return 1 column banner with optional bells and whistles   */
/***************************************************************************/
function banner() {
   $return = '';

   $return .= '<section class="content-panel one-column-banner" ';
						if(get_sub_field('banner_background')) { 
							$return .= 'style="background-image: url(' . get_sub_field('banner_background')['url'] . ');"';
						} 
	$return .=  '">
                <div class="container-fluid">
					   <div class="row">
						  <div class="col-xs-12">';
	                   if(get_sub_field('banner_h1')) { $return .= '<h1>' . get_sub_field('banner_h1') . '</h1>'; }
		                if(get_sub_field('banner_h2')) { 
								 $return .= '<h2>' . get_sub_field('banner_h2');
								 if(get_sub_field('banner_h2_image')) { 
									 $return .= '<img src="' . get_sub_field('banner_h2_image')['url'] . '" />';
								 }
								 $return .= '</h2>'; 
							 }
							 if(get_sub_field('banner_text')) { $return .= '<p>' . get_sub_field('banner_text') . '</p>'; }
	                   if(get_sub_field('banner_button_text')) { 
								 $return .= '<a class="btn universal-btn-red" href="' . get_sub_field('banner_button_link') . '">' .  get_sub_field('banner_button_text') . '</a>';
							 }

   $return .=       '</div>
	               </div>
	             </div>';
	$return .= '</section>'; 
   return $return;
}



/*********************************************************/
/*   Function to return 3 column with header and footer  */
/*********************************************************/
function get3columnsHeaderFooter() {
   $return = '';

   $return .= '<section class="content-panel three-column-header-footer">
                <div class="three-column-header text-center">
					   <img src="' . get_sub_field('column_header_image')['url'] . '" alt="' . get_sub_field('column_header_image')['alt'] . '" />
						<h2>' . get_sub_field('column_header_text') . '</h2>
					 </div>
                <div class="container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="col-sm-4 col-xs-12">
			              <a href="' . get_sub_field('column_link') . '" class="make-card">
							     <div class="column-info">
									  <div class="column-title">' . get_sub_field('column_title') . '</div>
									  <div class="column-name">' . get_sub_field('column_teacher') . '</div>
								  </div>
								  <img src="' . get_sub_field('column_picture')['url'] . '" alt="' . get_sub_field('column_picture')['alt'] . '" />
							  </a>
							</div>	  
			';
		}
              
   }
	if(get_sub_field('column_footer_text')) { // only show the footer if it exists
		$return .= '<div class="col-xs-12"><a class="btn universal-btn-red" href="' . get_sub_field('column_footer_link') . '">' . get_sub_field('column_footer_text') . '</a></div>';
	}

   $return .=     '</div>
	             </div>';
	$return .= '</section>'; 
   return $return;
}

/*********************************************************/
/*   Function to return 3 column with header only        */
/*********************************************************/
function get3columnsHeader() {
   $return = '';

   $return .= '<section class="content-panel three-column-header">
                <div class="three-column-header text-center">
						<h2>' . get_sub_field('column_header_text') . '</h2>
					 </div>
                <div class="container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="col-sm-4 col-xs-12">
			              <a href="' . get_sub_field('column_link') . '" class="make-card">
							     <img src="' . get_sub_field('column_picture')['url'] . '" alt="' . get_sub_field('column_picture')['alt'] . '" />
								  <div class="column-info">
									  <div class="column-title">' . get_sub_field('column_title') . '</div>
									  <div class="column-name">' . get_sub_field('column_teacher') . '</div>
								  </div>
							  </a>
							</div>	  
			';
		}
              
   }

   $return .=     '</div>
	             </div>';
	if(get_sub_field('column_footer_text')) {
		$return .= '<a class="btn universal-btn-red" href="' . get_sub_field('column_footer_link') . '">' . get_sub_field('column_footer_text') . '</a>';
	}
	$return .= '</section>'; 
   return $return;
}

/*********************************************************/
/*   Function to return 2 column with lists              */
/*********************************************************/
function get2columnsList() {
   $return = '';

   $return .= '<section class="content-panel 2-column-header">
                <div class="three-column-header text-center">
						<h2>' . get_sub_field('column_header_text') . '</h2>
					 </div>
                <div class="container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="col-sm-6 col-xs-12" ';
				if(get_sub_field('column_background')) { 
					$return .= 'style="background-image: url(' . get_sub_field('column_background')['url'] . ');"';
				} 
			$return .=  '">
							   <div class="column-info">
								   <div class="column-title">' . get_sub_field('column_title') . '</div>';
									if (have_rows('column_list')) {
										$return .= '<ul class="column-list">';
											// loop through the list data this goes on forever!!!!
											/*while (have_rows('column_list')) {
												$return .= '<li class="column-list-item">' . get_sub_field('column_list_item') . '</li>';
											}*/
										$return .= '</ul>';
									}
			$return .=     '</div>
							</div>	  
			';
		}
              
   }

   $return .=     '</div>
	             </div>';
	if(get_sub_field('column_footer_text')) {
		$return .= '<a class="btn universal-btn-red" href="' . get_sub_field('column_footer_link') . '">' . get_sub_field('column_footer_text') . '</a>';
	}
	$return .= '</section>'; 
   return $return;
}