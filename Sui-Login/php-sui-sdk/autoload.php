<?php

$composerLoader =  dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($composerLoader)) {
  require_once $composerLoader;
} else {
  _e('Install composer for current work');
  exit;
}

require_once dirname(__FILE__) . '/src/utils.php';
require_once dirname(__FILE__) . '/src/signature.php';
require_once dirname(__FILE__) . '/src/publickey.php';
require_once dirname(__FILE__) . '/src/verify.php';
