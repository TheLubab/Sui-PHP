<?php

namespace SuiPay;

use SuiPay\Admin\ConfigPage;
use SuiPay\Paywall\Paywall;
use SuiPay\ShortCode\ShortCode;

/**
 * Init all main functionality
 *
 * Class Main
 * @package SuiPay
 */
class Main
{
  public static mixed $options;

  // non-main Class instances
  protected array $instances = [];

  private function __construct()
  {
    $this->setUpActions();

    $this->instances['admin-config-page'] = new ConfigPage();
    $this->instances['short-code'] = new ShortCode();

    $this->instances['paywall'] = new Paywall();
  }


  private function setUpActions()
  {
    add_action('init', [&$this, 'addScripts'], 99);
    add_action('init', [&$this, 'addStyles'], 99);
  }

  public function addScripts()
  {
    wp_enqueue_script(
      'sui-pay',
      plugin_dir_url(__FILE__) . '../public/index.js',
      ['jquery'],
      '0.01'
    );
  }

  public function addStyles()
  {
    wp_enqueue_style('sui-pay', plugin_dir_url(__FILE__) . '../public/styles.css');
  }


  /*--------------- Singleton Overrides ---------------*/
  /** 
   * Self Main object.
   * @var Main $instance
   */
  private static Main $instance;

  protected function __clone()
  {
  }
  public function __wakeup()
  {
  }

  /**
   * Get self object
   *
   * @return Constructor object
   */
  public static function getInstance(): Main
  {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Method to use native functions as methods
   * @param $name
   * @param $arguments
   * @return bool|mixed
   */
  public function __call($name, $arguments)
  {
    if (function_exists($name)) {
      return call_user_func_array($name, $arguments);
    }
    return false;
  }
}
