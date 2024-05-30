<?php

namespace SuiLogin\Model\Constructor;

use SuiLogin\Model\Abstractions\AdminPages;
use SuiLogin\Helper\View;

/**
 * Class ConfigPage
 * @package SuiLogin\Model\Constructor
 */
class ConfigPage extends AdminPages
{
  const OPTIONS_GROUP = 'login-with-sui-config';

  const FILE_EXTENSION = 'php';

  public $config;

  public string $optionsGroup;

  public function __construct($config)
  {
    parent::__construct();
    $this->config = $config;
    $this->optionsGroup = $this->getOptionsGroup();
    $this->setUp();
  }

  public function addAdminPage()
  {
    add_menu_page(
      'sui-config',
      'Sui',
      'manage_options',
      'sui-config',
      [&$this, 'displaySettingsPage'],
      "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjBweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgMjAgMjAiIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgICA8cGF0aCBkPSJNMTAgMEMxNS41MjM2IDAgMjAgNC40NzY0IDIwIDEwQzIwIDE1LjUyMzYgMTUuNTIzNiAyMCAxMCAyMEM0LjQ3NjM5IDIwIDAgMTUuNTIzNiAwIDEwQzAgNC40NzY0IDQuNDc2MzkgMCAxMCAwTDEwIDBaTTE0LjQ1NTggNy45NzQyNkwxMC4xMTY1IDIuNjUwNUMxMC4wMzM1IDIuNTQ4OTEgOS44Nzg2NCAyLjU0ODkxIDkuNzk1NjkgMi42NTA1TDUuNDU2OTQgNy45NzQyNkw1LjQ0Mjk0IDcuOTkxOTlDNC42NDQ3IDguOTg2MzggNC4xNjY2MyAxMC4yNSA0LjE2NjYzIDExLjYyNTZDNC4xNjY2MyAxNC44MjkgNi43NTg4OSAxNy40MjYxIDkuOTU2MDggMTcuNDI2MUMxMy4xNTMzIDE3LjQyNjEgMTUuNzQ1NSAxNC44MjkgMTUuNzQ1NSAxMS42MjU2QzE1Ljc0NTUgMTAuMjUgMTUuMjY3NCA4Ljk4NjM4IDE0LjQ2OTIgNy45OTI2TDE0LjQ1NTIgNy45NzQ4N0wxNC40NTU4IDcuOTc0MjZMMTQuNDU1OCA3Ljk3NDI2Wk0xMy40MzE5IDguNzY3OTNDMTQuMDM1NiA5LjUyODU1IDE0LjM5NjYgMTAuNDkwNSAxNC4zOTY2IDExLjUzNjlDMTQuMzk2NiAxMi41ODMzIDE0LjAyNDYgMTMuNTc0NiAxMy40MDUxIDE0LjMzOTVMMTMuMzUxNCAxNC40MDU2TDEzLjMzNzQgMTQuMzIxOEMxMy4zMjUyIDE0LjI1MDggMTMuMzExMSAxNC4xNzg2IDEzLjI5NDcgMTQuMTA2NEMxMi45ODQzIDEyLjczODEgMTEuOTczMyAxMS41NjUgMTAuMzA5MSAxMC42MTQ3QzkuMTg1MjQgOS45NzQ2NSA4LjU0MTg5IDkuMjA0ODQgOC4zNzI5OSA4LjMyOTE3QzguMjYzODEgNy43NjMxNCA4LjM0NDkyIDcuMTk0NjYgOC41MDE2NCA2LjcwNzU3QzguNjU4MzcgNi4yMjEwOSA4Ljg5MTMyIDUuODEyOTMgOS4wODk1MSA1LjU2NzU1TDkuNzM3MDkgNC43NzMyNkM5Ljg1MDUyIDQuNjMzNzcgMTAuMDYzNCA0LjYzMzc3IDEwLjE3NjggNC43NzMyNkwxMy40MzI1IDguNzY3OTNMMTMuNDMxOSA4Ljc2NzkzWk02LjQ5NTQ4IDguNzUwODFMNi44ODMzMyA4LjI3NDExTDYuODk0OTIgOC4zNjIyNEM2LjkwNDA4IDguNDMxOTkgNi45MTU2NCA4LjUwMTc1IDYuOTI5MDcgOC41NzIxM0M3LjE4MDMgOS44OTQ0OSA4LjA3NzM0IDEwLjk5NjYgOS41NzY4MSAxMS44NTAyQzEwLjg4MDYgMTIuNTk0OSAxMS42Mzk4IDEzLjQ1MSAxMS44NTgxIDE0LjM4OTdDMTEuOTQ5NiAxNC43ODE0IDExLjk2NTQgMTUuMTY2OCAxMS45MjU4IDE1LjUwNEwxMS45MjMzIDE1LjUyNDhMMTEuOTA0NCAxNS41MzRDMTEuMzE2IDE1LjgyMjIgMTAuNjU0NCAxNS45ODQ0IDkuOTU1NTMgMTUuOTg0NEM3LjUwMzUxIDE1Ljk4NDQgNS41MTU1NiAxMy45OTMyIDUuNTE1NTYgMTEuNTM2M0M1LjUxNTU2IDEwLjQ4MTMgNS44ODIwNSA5LjUxMjY1IDYuNDk0MyA4Ljc0OTU5TDYuNDk1NDggOC43NTA4MUw2LjQ5NTQ4IDguNzUwODFaIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0ibm9uZSIKc3R5bGU9ImZpbGw6ICMyMkFFRkYhaW1wb3J0YW50OyIKIC8+Cjwvc3ZnPg==",
    );

    add_submenu_page(
      'sui-config',
      'Sui general config',
      'General',
      'manage_options',
      'sui-config',
    );
  }

  public function setUp()
  {
    add_filter('getLoginWithSuiOptions', [$this, 'getOptions']);
  }

  public function getOptionsGroup(): string
  {
    return self::OPTIONS_GROUP;
  }

  public function registerSettings()
  {
    register_setting(
      self::OPTIONS_GROUP,
      self::OPTIONS_GROUP
    );
  }

  public function displaySettingsPage()
  {
    $path = $this->config->getTemplatesPath() . '/' . self::OPTIONS_GROUP . '.' . self::FILE_EXTENSION;
    View::view($path, $this);
  }

  public function getOptions()
  {
    return get_option(self::OPTIONS_GROUP);
  }
}
