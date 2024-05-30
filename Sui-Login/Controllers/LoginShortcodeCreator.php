<?php

namespace SuiLogin\Controllers;

use SuiLogin\Model\Constructor\Constructor;

class LoginShortcodeCreator
{

  public function __construct()
  {
    add_shortcode('sui_login_button', [$this, 'suiLoginButton']);
  }

  public function suiLoginButton(): string
  {
    $options = Constructor::$options;
    $loginButtonText = $options['login_button_text'];
    $loginButtonExtraClasses = $options['login_button_extra_classes'];
    $logoutButtonText = $options['logout_button_text'];
    $logoutButtonExtraClasses = $options['logout_button_extra_classes'];

    if (get_current_user_id()) {
      $href = wp_logout_url();
      return "<a class='{$logoutButtonExtraClasses} sui-button logout-with-sui-link' href='{$href}' >{$logoutButtonText}</a>";
    }

    return "<a class='{$loginButtonExtraClasses} sui-button sui-button-light login-with-sui-link' href='#'>{$loginButtonText}</a>";
  }
}
