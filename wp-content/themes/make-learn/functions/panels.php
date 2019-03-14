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
         case 'three_column': // 3 COLUMN LAYOUT with HEADER and FOOTER
            $return = get3columns();            
            break;
			case 'three_column_cards': // 3 COLUMN LAYOUT with HEADER only
            $return = get3columnsCards();            
            break;
			case 'two_column_list': // 2 COLUMN LAYOUT WITH A LIST IN EACH BOx
            $return = get2columnsList();            
            break;
			case 'two_column_small': // 2 COLUMN LAYOUT WITH SMALLER CARDS
				$return = get2columnsSmall();
				break;
		   case 'two_column_flexible': // 2 COLUMN LAYOUT WITH OPTIONAL FORMS
				$return = get2columnsFlexible();
				break;
			case 'four_columns_flexible': // 4 COLUMN LAYOUT WITH OPTIONS INSIDE
				$return = get4columns();
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

   $return .= '<section class="content-panel one-column-banner ' . get_sub_field('custom_class') . '" ';
	               // height of the banner is variable based on contents and bg image
						if(get_sub_field('banner_background')) { 
							$return .= 'style="background-image: url(' . get_sub_field('banner_background')['url'] . ');';
							if(get_sub_field('banner_button_text')) {
								$return .= 'min-height: 287px;"';
							}else{
								$return .= '"';
							}
						} else {
							$return .= 'style="min-height: 140px;margin-bottom: -30px;"';
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
function get3columns() {
   $return = '';

   $return .= '<section class="content-panel three-column ' . get_sub_field('custom_class') . '">
                <div class="three-column-header text-center">';
	               if(get_sub_field('column_header_image')) {
							$return .= '<img src="' . get_sub_field('column_header_image')['url'] . '" alt="' . get_sub_field('column_header_image')['alt'] . '" />';
						}
	$return .= 		'<h2>' . get_sub_field('column_header_text') . '</h2>
					 </div>
                <div class="three-column-contents container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="column col-sm-4 col-xs-12">
			              <a href="' . get_sub_field('column_link') . '" class="make-panel">
							     <div class="column-info">
									  <div class="column-title">' . get_sub_field('column_title') . '</div>
									  <div class="column-text">' . get_sub_field('column_text') . '</div>
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

/********************************************************************/
/*   Function to return 3 column with makershare style cards        */
/********************************************************************/
function get3columnsCards() {
   $return = '';

   $return .= '<section class="content-panel three-column-cards ' . get_sub_field('custom_class') . '">
                <div class="container">
						 <div class="three-column-header text-center">
							<h2>' . get_sub_field('column_header_text') . '</h2>
						 </div>
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="column col-sm-4 col-xs-12">
			              <a href="' . get_sub_field('column_link') . '" class="make-card">
							  	  <div class="image-crop">
							       <img src="' . get_sub_field('column_picture')['url'] . '" alt="' . get_sub_field('column_picture')['alt'] . '" />
								  </div>
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

   $return .= '<section class="content-panel two-column ' . get_sub_field('custom_class') . '">';
	             if(get_sub_field('column_header_text')){
                  $return .= '<div class="two-column-header text-center">
						              <h2>' . get_sub_field('column_header_text') . '</h2>
					               </div>';
					 }
   $return .=   '<div class="container-fluid">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="column col-sm-6 col-xs-12" ';
				if(get_sub_field('column_background')) { 
					$return .= 'style="background-image: url(' . get_sub_field('column_background')['url'] . ');"';
				} 
			$return .=  '">
							   <div class="column-info">
								   <div class="column-title">' . get_sub_field('column_title') . '</div>';
									if (have_rows('column_list')) {
										$return .= '<ul class="column-list">';
											while (have_rows('column_list')) {
											   the_row();
												$return .= '<li class="column-list-item">' . get_sub_field('column_list_item') . '</li>';
											}
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

/*********************************************************/
/*   Function to return 2 column with smaller cards      */
/*********************************************************/
function get2columnsSmall() {
	
   $return = '';

   $return .= '<section class="content-panel two-column ' . get_sub_field('custom_class') . '">
	             <div class="container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="column col-md-6 col-sm-12">
							   <div class="small-card column-info">
								   <img src="' . get_sub_field('column_image')['url'] . ' "/>
								   <div class="column-text">' . get_sub_field('column_text') . '</div>
									<a class="btn universal-btn" href="' . get_sub_field('column_button_link') . '">' . get_sub_field('column_button_text') . '</a>
								</div>
							</div>	  
			';
		}    
   }
   $return .=     '</div>
	             </div>
	           </section>'; 
   return $return;
}

/*********************************************************/
/*   Function to return 2 column with optional forms     */
/*********************************************************/
function get2columnsFlexible() {
	
   $return = '';

   $return .= '<section class="content-panel two-column-flexible ' . get_sub_field('custom_class') . '">
	             <div class="container">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
      while (have_rows('column_contents')) {
         the_row();
			$return .= '<div class="column col-md-6 col-sm-12 col-xs-12">
							   <div class="column-box column-info">
								   <div class="column-header">' . get_sub_field('column_header') . '</div>
								   <div class="column-text">' . get_sub_field('column_text') . '</div>';
		   // might later want to add additional logic for different kinds of email lists than whatcounts
							   if(get_sub_field('column_form_action')) {
			$return .= 		  '<form class="form-inline sub-form" id="workshop-form" name="workshop-form" action="https://secure.whatcounts.com/bin/listctrl" method="post" target="_blank">
										<input type="hidden" class="slid" id="list_' . get_sub_field('column_form_action') . '_yes" name="slid_1" value="' . get_sub_field('column_form_action') . '">
										<input name="cmd" value="subscribe" type="hidden">
										<input name="multiadd" value="1" type="hidden">
			                     <input id="workshop-email" class="form-control nl-panel-input" name="email" placeholder="email address" required="" type="email">
                              <div class="submit-message hidden">Please enter a valid email</div>
										<a class="btn universal-btn" name="workshop-button" id="workshop-button">' . get_sub_field('column_button_text') . '</a>
				               </form>';
								} else {
			$return .= 		  '<a class="btn universal-btn" href="' . get_sub_field('column_button_link') . '">' . 
									  get_sub_field('column_button_text') . 
								  '</a>';				
								}
			
			$return .= 		'</div>
							</div>	  
			';
		}    
   }
   $return .=     '</div>
	             </div>
	           </section>'; 
   return $return;
}

/*********************************************************/
/*   Function to return 4 column with optional cards     */
/*********************************************************/
function get4columns() {
	
   $return = '';

   $return .= '<section class="content-panel four-column ' . get_sub_field('custom_class') . '">
	             <div class="container-fluid">
					   <div class="row">';
	
   if (have_rows('column_contents')) {
      // loop through the rows of data
		$rowCount = 0;
      while (have_rows('column_contents')) {
         the_row();
			$rowCount += 1;
			$return .= '<div class="column col-md-3 col-sm-12">';
			             if(get_sub_field('type') == "Card") {
			$return .=     '<a class="polaroid-card" href="' . get_sub_field('column_link') . '">
								   <div class="image-wrap" style="background:url(' . get_sub_field('column_image')['url'] . ') no-repeat;" /></div>
                           <div class="column-title">' . get_sub_field('column_title') . '</div>
								 </a>';
							 }else if(get_sub_field('type') == "Button") {
			$return .=     '<div class="column-contents">
			                  <div class="column-info">
								     <div class="column-title">' . get_sub_field('column_title') . '</div>
									  <div class="column-text">' . get_sub_field('column_text') . '</div>
									</div>
									<a class="btn universal-btn-red" href="' . get_sub_field('column_link') . '">' . get_sub_field('column_button_text') . '</a>
								 </div>';					 
							 }
			$return .= '</div>';
		}    
   }
   $return .=     '</div>
	             </div>
	           </section>'; 
   return $return;
}