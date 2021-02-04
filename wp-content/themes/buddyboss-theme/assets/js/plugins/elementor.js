( function ( $ ) {

    "use strict";

    window.BuddyBossThemeElementor = {
        init: function () {
            this.ignoreFitVids();
        },

        ignoreFitVids: function() {
            
            $( ".elementor-section[data-settings*='background_video_link']" ).addClass( 'fitvidsignore' );

        },

    };

    $( document ).on( 'ready', function () {
        BuddyBossThemeElementor.init();
    } );

} )( jQuery );
