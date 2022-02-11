<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="esaf esaf-onboarding wrap">
  <div class="esaf-container">
    <div class="esaf-onboarding-logo">
      <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/logo.svg'); ?>
    </div>
  </div>
  <div class="esaf-container">
    <h1><?php esc_html_e('Welcome and Thank You for Choosing Us!', 'easy-affiliate'); ?></h1>
  </div>
  <div class="esaf-onboarding-hero">
    <div class="esaf-container">
      <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/onboarding/hero.png'); ?>" srcset="<?php echo esc_url(ESAF_IMAGES_URL . '/onboarding/hero@2x.png'); ?> 2x" alt="">
    </div>
  </div>
  <div class="esaf-container esaf-onboarding-padded-container">
    <p>
      <?php
        printf(
          esc_html__('Easy Affiliate makes it easy to create your own salesforce of worker bees using WordPress. %1$sWatch the video tutorial%2$s, use our 3 minute setup wizard or %3$sread the full guide%4$s.', 'easy-affiliate'),
          '<a href="https://easyaffiliate.com/get-started-video" target="_blank">',
          '</a>',
          '<a href="https://easyaffiliate.com/docs-category/getting-started/" target="_blank">',
          '</a>'
        );
      ?>
    </p>
  </div>
  <div class="esaf-container esaf-onboarding-launch-wizard-button">
    <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-wizard')); ?>"><?php esc_html_e('Launch the Wizard', 'easy-affiliate'); ?><i class="ea-icon ea-icon-right-big"></i></a>

  </div>

  <?php if(ESAF_EDITION != 'easy-affiliate-pro') : ?>

    <div class="esaf-container esaf-onboarding-features">
      <h2><?php esc_html_e("Easy Affiliate's BUZZ-Worthy Features", 'easy-affiliate'); ?></h2>
      <p><?php esc_html_e('These are the features that make Easy Affiliate the most powerful and user-friendly WordPress affiliate plugin on the market.', 'easy-affiliate'); ?></p>
      <div class="esaf-features">
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/shapes.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Super-Simple Setup', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('If you know how to install a WordPress Plugin, you know how to launch an affiliate program with Easy Affiliate.', 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/tachometer.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Personalized Affiliate Dashboard', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('The Affiliate Dashboard comes pre-styled and can be adjusted to fix your brand using the WordPress Customizer.', 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/money-check.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Perfect Payment Integration', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('Set up tracking for initial and recurring payments from MemberPress, WooCommerce, and Easy Digital Downloads.', 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/user-crown.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Powerful Admin', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("You'll find everything you need to run your affiliate program right in your WordPress admin.", 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/file-check.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Affiliate Applications', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("Approve, ignore, or reject any application as it comes in with Easy Affiliate's built-in Affiliate Application process.", 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/analytics.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Commissions Tracking', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('Set your commission rate and customize it per user - even set multiple commission levels.', 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/shield-check.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Advanced Security', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("We're the most secure affiliate plugin on the market. Rest assured that your commissions are being tracked and your affiliate program is safe against attack.", 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/sensor-alert.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Fraud Detection', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('Easy Affiliate makes fraud detection simple by flagging or rejecting suspicious affiliate behavior BEFORE you pay out.', 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/palette.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Complete Creative Management', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("With our full-featured creative asset management platform, your marketing is always on point because you're in control of it.", 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/link.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Custom Link Generation', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("Built-in support for Pretty Links means your affiliates' custom links will always be short and look great.", 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/mouse-click.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Pay Affiliates Confidently', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('With support for PayPal Mass Payment files, offline payments and One Click payments via PayPal, paying affiliates is a breeze.', 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/envelope-open.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Intuitive Email Marketing', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e('Easy Affiliate integrates seamlessly with major email marketing services like Mailchimp, ActiveCampaign, and ConvertKit.', 'easy-affiliate'); ?></p>
            </div>
          </div>
        </div>
        <div class="esaf-feature-row">
          <div class="esaf-feature">
            <div class="esaf-feature-icon">
              <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/life-ring.svg'); ?>
            </div>
            <div class="esaf-feature-details">
              <h4><?php esc_html_e('Stellar Support', 'easy-affiliate'); ?></h4>
              <p><?php esc_html_e("In case you need us, out amazing support team is ready and willing to help. We won't leave you hanging.", 'easy-affiliate'); ?></p>
            </div>
          </div>
          <div class="esaf-feature"></div>
        </div>
      </div>
      <p><a href="https://easyaffiliate.com/features/" ><?php esc_html_e('See All Features', 'easy-affiliate'); ?></a></p>
    </div>
    <div class="esaf-onboarding-cta">
      <div class="esaf-onboarding-cta-inner">
        <div class="esaf-container">
          <div class="esaf-onboarding-cta-left">
            <div class="esaf-onboarding-cta-pro"><?php esc_html_e('PRO', 'easy-affiliate'); ?></div>
            <div class="esaf-onboarding-cta-price-section">
              <div class="esaf-onboarding-cta-normally">
                <?php
                  printf(
                    '<span>%s</span>',
                    esc_html(
                      sprintf(
                        // translators: %s: the normal price before discount
                        __('normally %s', 'easy-affiliate'),
                        '$299'
                      )
                    )
                  );
                ?>
              </div>
              <div class="esaf-onboarding-cta-price">
                <?php
                  printf(
                    // translators: %s: the price (per year)
                    esc_html__('%s/year', 'easy-affiliate'),
                    '<span><span>$</span>199</span>'
                  );
                ?>
              </div>
              <div class="esaf-onboarding-cta-savings">
                <?php
                  echo esc_html(sprintf(
                    // translators: %s: the savings amount
                    __('%s savings*', 'easy-affiliate'),
                    '$199.50'
                  ));
                ?>
              </div>
            </div>
            <div class="esaf-onboarding-cta-features">
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><?php esc_html_e('Use of Easy Affiliate on 5 websites', 'easy-affiliate'); ?></span>
              </div>
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><strong><?php esc_html_e('Everything in Plus and:', 'easy-affiliate'); ?></strong></span>
              </div>
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><?php esc_html_e('Use Commission Rules Add-On', 'easy-affiliate'); ?></span>
              </div>
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><?php esc_html_e('Use ActiveCampaign Add-On', 'easy-affiliate'); ?></span>
              </div>
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><?php esc_html_e('Use Developer Tools (Coming Soon)', 'easy-affiliate'); ?></span>
              </div>
              <div class="esaf-onboarding-cta-feature">
                <i class="ea-icon ea-icon-right-big"></i>
                <span><?php esc_html_e('Premium Support', 'easy-affiliate'); ?></span>
              </div>
            </div>
          </div>
          <div class="esaf-onboarding-cta-right">
            <h2><?php esc_html_e('Upgrade to Pro', 'easy-affiliate'); ?></h2>
            <p>
              <?php
                printf(
                  // translators: %s: the savings amount
                  esc_html__('Perfect for eCommerce site builders to drive big results. Upgrade to Pro today and save %s.', 'easy-affiliate'),
                  '$199.50'
                );
              ?>
            </p>
            <p>
              <a href="https://easyaffiliate.com/pricing/" class="esaf-onboarding-button"><?php esc_html_e('Upgrade to PRO Now', 'easy-affiliate'); ?></a>
              <a href="https://easyaffiliate.com/features/" class="esaf-see-all-features-button"><?php esc_html_e('See All Features', 'easy-affiliate'); ?></a>
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="esaf-container esaf-onboarding-testimonials-container">
      <h2>
        <?php
          printf(
            // translators: %s: love heart
            esc_html__('Customers %s Easy Affiliate', 'easy-affiliate'),
            file_get_contents(ESAF_IMAGES_PATH . '/onboarding/heart.svg')
          );
        ?>
      </h2>
      <div class="esaf-onboarding-testimonials">
        <div class="esaf-onboarding-testimonial">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/five-stars.svg'); ?>
          <div class="esaf-onboarding-testimonial-quote">
            "If you're looking for a simple but powerful plugin to create and run your own affiliate program,
            <br>
            <strong>Easy Affiliate is the answer."</strong>
          </div>
          <div class="esaf-onboarding-testimonial-cite">
            <div class="esaf-onboarding-testimonial-cite-left">
              <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/onboarding/syed.png'); ?>" alt="">
            </div>
            <div class="esaf-onboarding-testimonial-cite-right">
              <span>Syed Balkhi,<br>Founder of WPBeginner</span>
            </div>
          </div>
        </div>
        <div class="esaf-onboarding-testimonial">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/five-stars.svg'); ?>
          <div class="esaf-onboarding-testimonial-quote">
            "Easy Affiliate's thoughtful developer-focused features have been instrumental in enabling us to
            <br>
            <strong>to tap into the marketing power of our affiliates."</strong>
          </div>
          <div class="esaf-onboarding-testimonial-cite">
            <div class="esaf-onboarding-testimonial-cite-left">
              <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/onboarding/brandon.png'); ?>" alt="">
            </div>
            <div class="esaf-onboarding-testimonial-cite-right">
              <span>Brandon Dove,<br>Co-Founder of PixelJar</span>
            </div>
          </div>
        </div>
        <div class="esaf-onboarding-testimonial">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/onboarding/five-stars.svg'); ?>
          <div class="esaf-onboarding-testimonial-quote">
            "A complete WordPress affiliate plugin that helps you get your products out there,
            <br>
            <strong>increase traffic, attention and, ultimately, sales."</strong>
          </div>
          <div class="esaf-onboarding-testimonial-cite">
            <div class="esaf-onboarding-testimonial-cite-left">
              <img src="<?php echo esc_url(ESAF_IMAGES_URL . '/onboarding/jack.png'); ?>" alt="">
            </div>
            <div class="esaf-onboarding-testimonial-cite-right">
              <span>Jack Coble,<br>Co-Founder of Pilatesology</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="esaf-container esaf-onboarding-padded-container">
      <p class="esaf-onboarding-buttons">
        <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-wizard')); ?>" class="esaf-onboarding-button"><?php esc_html_e('Launch the Wizard', 'easy-affiliate'); ?></a>
      </p>
    </div>

  <?php endif; ?>

</div>
