// newsletter signup logic

var newsletterSignupManager = function() {

   var vendors = {
         'mailchimp': {
            'postURL': 'https://makermedia.us9.list-manage.com/subscribe/post?u=4e536d5744e71c0af50c0678c&id=',
            'emailSel': '#EMAIL', // NOTE (ts): it turns out this id is VERY important for proper form operation, do not change!
            'templateSel': '#mc-form-template' // not currently used
         },
         'whatcounts': {
            'postURL': 'https://secure.whatcounts.com/bin/listctrl',
            'emailSel': '#wc-email', // NOTE (ts): it turns out this id is VERY important for proper form operation, do not change!
            'templateSel': '#wc-form-template'
         }
      },
      currentSite = false,
      currentSettings = false,
      sites = {
         'makeco': {
            'domains': 'makeco, makeco.wpengine.com, makeco.staging.wpengine.com, makeco.com, make.co, stage.make.co, dev.make.co',
            'header': 'Sign up for the Make: Membership newsletter',
            'listID': '609e43360a',
            'groupID': '38209',
            'vendor': 'mailchimp',
            'beltOff': true
         },
         'makermedia': {
            'domains': 'maker-media, makermedia, makermedia.wpengine.com, makermedia.staging.wpengine.com, makermedia.com, stage.makermedia.com, dev.makermedia.com',
            'header': 'Sign up for the Make: magazine newsletter',
            'listID': '6B5869DC547D3D46B52F3516A785F101',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
         'makerfaire': {
            'domains': 'maker-faire, makerfaire, makerfaire.wpengine.com, makerfaire.staging.wpengine.com, makerfaire.com, stage.makerfaire.com, dev.makerfaire.com',
            'header': 'Sign up for the Maker Faire newsletter',
            'listID': '6B5869DC547D3D46E66DEF1987C64E7A',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
         'makezine': {
            'domains': 'make-zine, makezine, makezine.wpengine.com, makezine.staging.wpengine.com, makezine.com, stage.makezine.com, dev.makezine.com',
            'header': 'Sign up for the Make: magazine newsletter',
            'listID': '6B5869DC547D3D46B52F3516A785F101',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
         'makershed': {
            'domains': 'makershed, www.makershed.com',
            'header': 'Sign up for the Maker Shed newsletter',
            'listID': '6B5869DC547D3D46510F6AB3E701BA0A',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
         'makerspaces': {
            'domains': 'makerspaces, makerspaces.wpengine.com, makerspaces.staging.wpengine.com, spaces.makerspace.com, makerspaces.make.co, stage.makerspace.com, dev.makerspace.com',
            'header': 'Stay in the loop with our Maker Pro newsletter',
            'listID': '6B5869DC547D3D467B33E192ADD9BE4B',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
         'makershare': {
            'domains': 'maker-share, makershare, makeshare.wpengine.com, makershare.staging.wpengine.com, makershare.com',
            'header': 'Sign up for the Maker Share newsletter',
            'listID': '6B5869DC547D3D46072290AE725EC932',
            'groupID': false,
            'vendor': 'whatcounts',
            'beltOff': true
         },
      },
      // Get the current host
      currentHost = window.location.hostname,
      //console.log(currentHost);
      _publicNewsletterSignupManager = false; // public object


/* =============   Initialize the settings   ================  */

   // loop thru sites object
   for(var site in sites) {
      var siteDomainsArray = [];
      // if we don't already have a current site 
      if(!currentSite && sites.hasOwnProperty(site)) {
         //console.log(site, sites[site]);
         // make an array out of the domain list for this site, trim spaces
         siteDomainsArray = sites[site].domains.replace(/ /g,'').split(',');
         //console.log(siteDomainsArray);
         // if our current host matches something in the domains list fo this site
         // set it as the current site and grab the settings
         if(siteDomainsArray.indexOf(currentHost) > -1) {
            currentSite = site;
            currentSettings = sites[site];
            // import the vendor settings into the site settings
            for(setting in vendors[currentSettings.vendor]) {
               if(vendors[currentSettings.vendor].hasOwnProperty(setting)) {
                  currentSettings[setting] = vendors[currentSettings.vendor][setting];
               }
            }
         }
      }
   }

/* =============   Instantiate the public object   ================  */
   // if we have a current site based on the host, populate the public object
   if(currentSite) {

      _publicNewsletterSignupManager = {};
      _publicNewsletterSignupManager.$genericContainer = jQuery('#subscribe-form-container');
      // TBD (ts): cache any other selections used more than once
      _publicNewsletterSignupManager.site = currentSite;
      _publicNewsletterSignupManager.host = currentHost;
      _publicNewsletterSignupManager.settings = currentSettings;
      // _publicNewsletterSignupManager.formValid = false; // TBD (ts): don't really need this currently, but could make it more formal using this
      _publicNewsletterSignupManager.validateCheckboxes = function() {
         var valid = false,
            $checkboxes = _publicNewsletterSignupManager.$genericContainer.find('input[type="checkbox"]'),
            $errorMessage = jQuery('.makerfaire-checkboxes').find('.error-message');
         $checkboxes.each(function(idx, el) {
            if(jQuery(el).is(':checked')) {
               valid = true;
               $errorMessage.addClass('hidden');
            }
         });
         if(!valid) {
            $errorMessage.removeClass('hidden');
         }
         return valid;
      }
      _publicNewsletterSignupManager.validateEmail = function() {
         var valid = false,
            $emailInput = _publicNewsletterSignupManager.$genericContainer.find(_publicNewsletterSignupManager.settings.emailSel);
         if($emailInput[0].checkValidity()) {
            $emailInput.next().addClass('hidden');
            valid = true;
         } else {
            $emailInput.next().removeClass('hidden');
         }
         return valid;
      }

      // Replace the form for Whatcounts if applicable - NOTE (ts): eventually we could remove this?
      if(_publicNewsletterSignupManager.settings.vendor === 'whatcounts') {
         _publicNewsletterSignupManager.$genericContainer.children().remove();
         // var $formTemplate = jQuery(_publicNewsletterSignupManager.settings.templateSel + '-container').html();
         // console.log($formTemplate);
         _publicNewsletterSignupManager.$genericContainer.append(jQuery(_publicNewsletterSignupManager.settings.templateSel + '-container').html());
         var $slidInput = _publicNewsletterSignupManager.$genericContainer.find('.slid');
         $slidInput.val(_publicNewsletterSignupManager.settings.listID).attr('id', 'list_' + _publicNewsletterSignupManager.settings.listID + '_yes');
         _publicNewsletterSignupManager.$genericContainer.find('#custom_source').val(window.location.hostname + window.location.pathname);
         _publicNewsletterSignupManager.$genericContainer.find('#custom_host').val(window.location.hostname);
         if(_publicNewsletterSignupManager.site !== 'makerfaire') {
            _publicNewsletterSignupManager.$genericContainer.find('.makerfaire-checkboxes').remove();
         }
      } else {
         // TBD (ts): eventually we may also need to do something with the groupID here
         _publicNewsletterSignupManager.settings.postURL += _publicNewsletterSignupManager.settings.listID;
      }

      _publicNewsletterSignupManager.toggledClosed = false,
      _publicNewsletterSignupManager.$footer = jQuery(".universal-footer"),
      _publicNewsletterSignupManager.$newsletterFooter = jQuery(".newsletter-footer")

      _publicNewsletterSignupManager.beltOff = function() {
         _publicNewsletterSignupManager.$newsletterFooter.removeClass("scrolling");
         localStorage.setItem("newsletterClosed", "yes");
         _publicNewsletterSignupManager.toggledClosed = true;
      }
      if(!localStorage.newsletterClosed && !jQuery('html').hasClass('mobile')) {
         _publicNewsletterSignupManager.$newsletterFooter.addClass("scrolling");
         jQuery(document).scroll(function() {
            if (_publicNewsletterSignupManager.toggledClosed === false && (window.innerHeight + window.pageYOffset + (_publicNewsletterSignupManager.$footer.height() - _publicNewsletterSignupManager.$newsletterFooter.innerHeight())) <= document.body.offsetHeight - 25) {		
               _publicNewsletterSignupManager.$newsletterFooter.addClass("scrolling");
            } else  {
               _publicNewsletterSignupManager.$newsletterFooter.removeClass("scrolling");
            }
         });
      }

      if(_publicNewsletterSignupManager.settings.beltOff) {
         _publicNewsletterSignupManager.beltOff();
      }

      // Set the header text from settings
      jQuery(".newsletter-section h3").text(_publicNewsletterSignupManager.settings.header);

      // Bind the subscribe button
      jQuery('#subscribe-button').on('click',function(event) {
         // Hide the error messages initially - not currently working right
         _publicNewsletterSignupManager.$genericContainer.find('.error-message').addClass('hidden');
         // If makerfaire, validate the checkboxes
         // if(_publicNewsletterSignupManager.site === 'makerfaire') {
         //    // if not valid, just return and don't check email
         //    if(!_publicNewsletterSignupManager.validateCheckboxes()) {
         //       event.preventDefault(); // prevents fall-through to form 'action' attr
         //       return;
         //    }
         // }
         // if makerfaire and valid checkboxes, or for any other site without checkboxes, validate email
         if(_publicNewsletterSignupManager.validateEmail()) {
            event.preventDefault(); // prevents fall-through to form 'action' attr
            // trigger recpatcha manually
            grecaptcha.execute();
         } else {
            // NOTE (ts): in browsers that support html5 validation, the next line prevents that validation from showing, so commented
            // Have to determine if this presents a problem for browsers that DON'T suppoprt html5 validation
            //event.preventDefault(); // prevents fall-through to form 'action' attr
            return;
         }

      });

   } // end if currentSite

/* =============   Return the public object   ================  */
   // if we didn't find a current site, we're returning a false here
   // which means we're falling thru to the default form (mailchimp), list (membership), and form action (action attr on form element)
   return _publicNewsletterSignupManager;
}

// call our main function when page ready
jQuery(document).ready(function(){
   var signupManager = newsletterSignupManager();

   // recaptcha callback, exposed via window object
   window.onRecaptchaValid = function() {
      // if we set up a site, define a real function here
      if(signupManager) {
         //console.log(signupManager);
         var email = jQuery(signupManager.settings.emailSel).val();
         jQuery('#subscribe-button').addClass('working');
         jQuery.post(signupManager.settings.postURL, jQuery('#subscribe-form').serialize())
            .done(function(data) {
               // can't use this because mc returns a CORS error
               // console.log('success', data);
            })
            .fail(function(err) {
               // can't use this because mc returns a CORS error
               // console.log('error', err);
            })
            .always(function(data){
               // For now we'll use the `always` clause
               //console.log(data);
               jQuery('#subscribe-button').removeClass('working');
               jQuery(signupManager.settings.emailSel).val("");
               jQuery('.makerfaire-checkboxes input[type="checkbox').each(function(idx,el) {
                  jQuery(el).prop('checked', false);
               });
               
               jQuery('.fancybox-thx').trigger('click');
               jQuery('.nl-modal-email-address').text(email);
               jQuery('.whatcounts-signup2 #email').val(email);
               signupManager.beltOff();
            });
      } else {
         // otherwise just return true
         return true;
      }
   }


   //get current year for footer, if we could include a php file from a different domain, we'd do it that way
   var currentYear = (new Date()).getFullYear();
   jQuery(".footer-copyright .current-year").text(currentYear);

   jQuery(".close-newsletter").on('click', function () {
      beltOff();
   });

});
