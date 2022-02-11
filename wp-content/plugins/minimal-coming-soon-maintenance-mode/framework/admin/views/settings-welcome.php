<div class="csmm-tile" id="welcome">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Welcome</div>
        <p>Thank you for choosing the Coming Soon PRO plugin! Videos and short tutorials in this section will help you get started.</p>

        <div class="csmm-section-content">

            <div class="home-box"><span>Where's the main on/off button?</span>
                <p>In <a href="#basic" class="csmm-change-tab">main settings</a>. But it's also always available in the admin bar (top of the screen).</p>
            </div>

            <div class="home-box"><span>How do I show the site to my friends or clients?</span>
                <p>Open <a href="#access" class="csmm-change-tab">Access</a> settings. Send them the Secret Access Link or whitelist their IP.</p>
            </div>

            <div class="home-box"><span>Where are emails from the subscribe form saved?</span>
                <p>In the configured <a href="#email" class="csmm-change-tab">autoresponder/mailing service</a>. Emails are not stored locally.</p>
            </div>

            <div class="home-box"><span>I want to collect emails</span>
                <p>Browse <a href="#themes" class="csmm-change-tab">themes</a> and activate one that fits your needs. If it doesn't already have a <i>Subscribe Form</i> module add it via the <a href="#design-layout" class="csmm-change-tab">Layout manager</a>. If you're using MailChimp, configure the API key in <a href="#email" class="csmm-change-tab">Autoresponder Services</a>, if not use the Universal Autoresponder Services to connect to your mailing system. Don't forget to enable the coming soon mode and save settings.</p>
            </div>
            <div class="home-box"><span>I want to set up a long-term coming soon page</span>
                <p>Browse <a href="#themes" class="csmm-change-tab">themes</a> and activate a theme that fits your needs. Use the <a href="#design-layout" class="csmm-change-tab">Layout manager</a> to add/remove modules and then edit them individually to adjust settings. Spend as much time as needed on <a href="#seo" class="csmm-change-tab">SEO</a> to maximise your search engine visibility. Add your Google Analytics Tracking ID, save settings, and enable the coming soon mode.</p>
            </div>
            <div class="home-box"><span>I need to set up multiple domains/sites</span>
                <p>Open the first domain, browse <a href="#themes" class="csmm-change-tab">themes</a>, and activate one that (mostly) fits all domains. Adjust all settings, <a href="#design-layout" class="csmm-change-tab">appearance</a>-related as well as <a href="#access" class="csmm-change-tab">access</a> ones. Save settings, preview the page &amp; when you're satisfied open <a href="#advanced" class="csmm-change-tab">advanced settings</a>, scroll to the bottom and Export Settings. Login to other domains and use Import Settings function to transfer settings over.</p>
            </div>
            <div class="home-box"><span>I can't find a theme that fits my needs</span>
                <p>Sorry to hear that. If it's just a matter of wrong colors or a <i>not-so-ideal</i> background image you'll be able to easily adjust those in <a class="csmm-change-tab" href="#design-background">background settings</a> and in individual module settings.

                    <?php if (csmm_whitelabel_filter()) { ?>
                        <br>If you're looking for a completely different layout, let our <a class="csmm-change-tab" href="#support">support</a> know. Send them a sketch or a link to the page you'd like to clone, and they'll see if we can add such a theme ASAP.
                    <?php } ?>
                </p>
            </div>
            <?php if (csmm_whitelabel_filter()) { ?>
                <div class="home-box"><span>I need support</span>
                    <p>Our in-house support agents will be happy to answer any questions you have! But, before you email us, we'd like to turn your attention to our <a href="https://docs.comingsoonwp.com/" target="_blank">detailed online documentation</a> as well as a whole set of <a href="https://comingsoonwp.com/video-tutorials/">video tutorials</a>. Still stuck? <a href="#open-beacon">Contact support</a>.<br>Our average response time on weekdays is one hour.</p>
                </div>
                <div class="home-box"><span>Getting started tutorial</i></span>
                    <div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/-bEJ-mPpduM" frameborder="0" allow="encrypted-media" allowfullscreen></iframe></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div><!-- #welcome -->