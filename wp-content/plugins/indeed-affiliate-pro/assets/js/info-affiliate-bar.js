/*
 *Ultimate Affiliate Pro - Front-end Affiliate Bar
 */
"use strict";
jQuery( document ).on( 'ready', function(){
  if ( jQuery('#uap_js_info_affiliate_bar_links_trigger').length ){
      jQuery( '#uap_js_info_affiliate_bar_links_trigger' ).popover({
            content       : jQuery('#uap_js_iab_linkts_triggers_section_data').html(),
            interface     : 'popover',
            position      : 'bottom-left',
            trigger       : 'click',
            trigger_off   : 'click',
            theme		      : 'uap-links-section',
            title 		    : jQuery('.uap-js-iab-simple-affiliate-link-label').attr('data-value'),
      });
      jQuery('#uap_js_iab_linkts_triggers_section_data').html('');

      jQuery( document ).on( 'change', '#uap_affiliate_bar_friendly_links', function( e ){
          uapInfoAffiliateBarUpdateLink();
      });
  }

  jQuery( document ).on( 'change', '#ap_affiliate_bar_ref_type', function( e ){
      uapInfoAffiliateBarUpdateLink();
  });

    if ( jQuery('#uap_info_affiliate_bar_banner_section').length ){
        jQuery( '#uap_info_affiliate_bar_banner_section' ).popover({
              content       : jQuery('#uap_js_iab_banner_section_data').html(),
              interface     : 'popover',
              position      : 'bottom-left',
              trigger       : 'click',
              trigger_off   : 'click',
              theme		      : 'uap-banners-section',
              title 		    : jQuery( '.uap-js-iab-banner-affiliate-link-label' ).attr('data-value'),
        });
        jQuery('#uap_js_iab_banner_section_data').html( '' );
    }

  jQuery( document ).on( 'change', '#uap_iab_banner_select_size', function( e ){
        uapInfoAffiliateBarChangeBannerSize( jQuery( '#uap_iab_banner_select_size' ).val() );
  });
});
