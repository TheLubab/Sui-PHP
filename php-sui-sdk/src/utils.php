<?php

/**
 * Checks if str is a valid address, with or without 0x 
 *
 * @param string $str The sui address
 * @return bool 
 */
function sui_is_valid_address($str)
{
  $regex = '/^(0x)?[0-9a-fA-F]{64}$/';

  if (empty($str)) {
    return false;
  }

  if (preg_match($regex, $str)) {
    return true;
  } else {
    return false;
  }
}
