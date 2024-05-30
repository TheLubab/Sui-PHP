  <div class="sui-wrap">
    <h1 class="sui-h1">
      Paywall Settings
    </h1>
    <form method="post" action="" class="sui-settings-form">
      <div class="sui-form-field">
        <label class="sui-label" for="sui-paywall-type">
          Type
        </label>
        <select class="sui-select" id="sui-paywall-type" name="paywall_type">
          <option value="one-time" <?php selected($paywall_type, 'one-time'); ?>>One-Time</option>
          <option value="subscription" <?php selected($paywall_type, 'subscription'); ?>>Subscription</option>
        </select>
      </div>
      <div class="sui-form-field">
        <label class="sui-label" for="login_button_text">
          Price (SUI)
        </label>
        <input class="sui-input" type="number" min="1" max="100000" name="paywall_price" value="<?php echo esc_attr($paywall_price); ?>" />
      </div>
      <div class="sui-form-field">
        <label class="sui-label" for="login_button_text">
          Payment Address
        </label>
        <input class="sui-input" required="required" type="text" name="paywall_receiver" placeholder="0x..." value="<?php echo esc_attr($paywall_receiver); ?>" />
      </div>
      <div class="sui-form-field">
        <label class="sui-label" for="login_button_text">
          Paywalled Categories
        </label>
        <fieldset>
          <?php foreach ($categories as $category) : ?>
            <label>
              <input type="checkbox" name="paywall_categories[]" value="<?php echo $category->term_id; ?>" <?php checked(in_array($category->term_id, $paywall_categories)); ?>>
              <?php echo $category->name; ?>
            </label>
          <?php endforeach; ?>
        </fieldset>
      </div>
      <?php submit_button(); ?>
    </form>
  </div>
