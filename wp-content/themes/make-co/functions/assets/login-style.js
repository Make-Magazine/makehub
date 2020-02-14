jQuery(window).load(function(){
   jQuery(".auth0-login #auth0-login-form .auth0-lock").prepend("<button class='join-btn'><a href='/join'>Join Now</a></button>").delay( 4000 );
   jQuery(".auth0-lock-submit").before("<a style='font-size:15px;color:rgba(0,0,0,0.87);text-align:center;margin:-16px 15px 0px;padding:10px;line-height:1.2em;display:block;font-weight:normal;font-style:italic;' href='https://readerservices.makezine.com/mk/SubInfo.aspx?PC=MK' target='_blank'>Access your Make: Magazine subscription <u>here</u></a><br />");
	jQuery(".auth0-lock-tabs li:last-of-type").remove();
});