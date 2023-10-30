    <div id="mepr-activecampaign" class="mepr-autoresponder-config">
      <input type="checkbox" name="mepractivecampaign_enabled" id="mepractivecampaign_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="mepractivecampaign_enabled"><?php _e('Enable ActiveCampaign', 'memberpress-activecampaigntags'); ?></label>
    </div>
    <div id="activecampaign_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-activecampaign-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-activecampaign-message" class="mepr-hidden mepr-active"></div>
      <div id="mepractivecampaign-account">
        <label>
          <span><?php _e('ActiveCampaign Account:', 'memberpress-activecampaigntags'); ?></span>
          <input type="text" name="mepractivecampaign_account" id="mepractivecampaign_account" value="<?php echo $this->account(); ?>" class="mepr-text-input form-field" size="20" />
        </label>
        <div>
          <span class="description">
            <?php _e('Your ActiveCampaign account ID. Typically something like: 1234567890123', 'memberpress-activecampaigntags'); ?><br />
            <i>(<?php _e('If you have a newer ActiveCampaign account, the ID may be your username (the bold part in the URL you use to access ActiveCampaign like so: http://<b>username</b>.activehosted.com/admin/', 'memberpress-activecampaign'); //Translators: Leave the <b> and </b> around username. ?>)</i>
          </span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-api-key">
        <label>
          <span><?php _e('ActiveCampaign API Key:', 'memberpress-activecampaigntags'); ?></span>
          <input type="text" name="mepractivecampaign_api_key" id="mepractivecampaign_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="90" />
          <span id="mepr-activecampaign-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-activecampaign-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API key under your Account settings at activecampaign.com.', 'memberpress-activecampaigntags'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-list-id">
        <label>
          <span><?php _e('ActiveCampaign List:', 'memberpress-activecampaigntags'); ?></span>
          <select name="mepractivecampaign_list_id" id="mepractivecampaign_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
        </label>
        <div>
          <span class="description"><?php _e('This is the list that new members will be added to upon signup.', 'memberpress-activecampaigntags'); ?></span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-form-id">
        <label>
          <span><?php _e('ActiveCampaign Form:', 'memberpress-activecampaigntags'); ?></span>
          <select name="mepractivecampaign_form_id" id="mepractivecampaign_form_id" data-formid="<?php echo $this->form_id(); ?>" class="mepr-text-input form-field"></select>
        </label>
        <div>
          <span class="description"><?php _e('(optional) This is here to enable double opt-in settings for contacts that are added through MemberPress. To get this working, create a form in ActiveCampaign with double-opt in setup and associate it with MemberPress here.', 'memberpress-activecampaigntags'); ?></span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-optin-tags">
        <label>
          <span><?php _e('ActiveCampaign Tags:', 'memberpress-activecampaigntags'); ?></span>
          <input type="text" name="mepractivecampaign_tags" id="mepractivecampaign_tags" value="<?php echo $this->global_tags(); ?>" class="form-field" size="75" />
        </label>
        <div>
          <span class="description"><?php _e('(optional) These tags will be added after the successful signup of a member. To add multiple tags separate with commas.', 'memberpress-activecampaigntags'); ?></span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-optin">
        <label>
          <input type="checkbox" name="mepractivecampaign_optin" id="mepractivecampaign_optin" <?php checked($this->is_optin_enabled()); ?> />
          <span><?php _e('Enable Opt-In Checkbox', 'memberpress-activecampaign'); ?></span>
        </label>
        <div>
          <span class="description">
            <?php _e('If checked, an opt-in checkbox will appear on all of your product registration pages.', 'memberpress-activecampaign'); ?>
          </span>
        </div>
      </div>
      <div id="mepractivecampaign-optin-text" class="mepr-hidden mepr-options-panel">
        <label><?php _e('Signup Checkbox Label:', 'memberpress-activecampaign'); ?>
          <input type="text" name="mepractivecampaign_text" id="mepractivecampaign_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
        </label>
        <div>
          <span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-activecampaign'); ?></span>
        </div>
      </div>
    </div>
