<div class="mp-form-row">
  <div class="mepr-activecampaign-signup-field">
    <div id="mepr-activecampaign-checkbox">
      <input type="checkbox" name="mepractivecampaign_opt_in" id="mepractivecampaign_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
      <span class="mepr-activecampaign-message"><?php echo $this->optin_text(); ?></span>
    </div>
    <div id="mepr-activecampaign-privacy">
      <small>
        <a href="http://www.activecampaign.com/help/privacy-policy/" class="mepr-activecampaign-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-activecampaigntags'); ?></a>
      </small>
    </div>
  </div>
</div>
