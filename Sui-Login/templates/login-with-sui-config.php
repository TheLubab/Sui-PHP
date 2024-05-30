<?php

use SuiLogin\Model\Constructor\Constructor;

ob_start(); ?>
<?php if (isset($args) && isset($args->optionsGroup)) : ?>
  <?php
  $options = Constructor::$options;
  $loginButtonText = $options['login_button_text'] ?? 'Login with Sui';
  $loginButtonExtraClasses = $options['login_button_extra_classes'] ?? '';
  $logoutButtonText = $options['logout_button_text'] ?? 'Logout';
  $logoutButtonExtraClasses = $options['logout_button_extra_classes'] ?? '';
  $network = $options['network'] ?? 'testnet';
  ?>
  <div class="sui-wrap">
    <h1 class="sui-h1"><?php esc_html_e('Sui General Configuration', 'tb-login-with-sui'); ?></h1>
    <form method="post" action="options.php" class="sui-settings-form">
      <?php settings_fields($args->optionsGroup); ?>
      <div class="sui-form-field">
        <label class="sui-label" for="login_button_text"><?php esc_html_e('Login Button Text', 'tb-login-with-sui'); ?></label>
        <input class="sui-input" type="text" id="login_button_text" name="<?php echo esc_attr("$args->optionsGroup[login_button_text]"); ?>" value="<?php echo esc_attr($loginButtonText); ?>" size="50" />
      </div>

      <div class="sui-form-field">
        <label class="sui-label" for="login_button_extra_classes"><?php esc_html_e('Login Button Extra Classes', 'tb-login-with-sui'); ?></label>
        <input class="sui-input" type="text" id="login_button_extra_classes" name="<?php echo esc_attr("$args->optionsGroup[login_button_extra_classes]"); ?>" value="<?php echo esc_attr($loginButtonExtraClasses); ?>" size="50" />
      </div>

      <div class="sui-form-field">
        <label class="sui-label" for="logout_button_text"><?php esc_html_e('Logout Button Text', 'tb-login-with-sui'); ?></label>
        <input class="sui-input" type="text" id="logout_button_text" name="<?php echo esc_attr("$args->optionsGroup[logout_button_text]"); ?>" value="<?php echo esc_attr($logoutButtonText); ?>" size="50" />
      </div>

      <div class="sui-form-field">
        <label class="sui-label" for="logout_button_extra_classes"><?php esc_html_e('Logout Button Extra Classes', 'tb-login-with-sui'); ?></label>
        <input class="sui-input" type="text" id="logout_button_extra_classes" name="<?php echo esc_attr("$args->optionsGroup[logout_button_extra_classes]"); ?>" value="<?php echo esc_attr($logoutButtonExtraClasses); ?>" size="50" />
      </div>

      <div class="sui-form-field">
        <label class="sui-label" for="network"><?php esc_html_e('Network', 'tb-login-with-sui'); ?></label>
        <select class="sui-select" id="network" name="<?php echo esc_attr("$args->optionsGroup[network]"); ?>">
          <option value="mainnet" <?php selected($network, 'mainnet'); ?>><?php esc_html_e('Mainnet', 'tb-login-with-sui'); ?></option>
          <option value="testnet" <?php selected($network, 'testnet'); ?>><?php esc_html_e('Testnet', 'tb-login-with-sui'); ?></option>
        </select>
      </div>

      <button class="sui-button sui-button-primary"><?php esc_html_e('Save', 'tb-login-with-sui'); ?></button>
    </form>
  </div>
<?php endif; ?>
<?php return ob_get_clean(); ?>
