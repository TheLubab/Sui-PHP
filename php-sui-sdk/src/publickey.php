<?php

/**
 * Convert Public Key Extrated From Signature to SUI Address Hex
 *
 * @param string $pk The public key bytes to be converted.
 * @param int $flag The ASCII value of the flag byte to prepend to the public key. Defaults to 0x00 (ED25519).
 *
 * @return string Returns the hexadecimal representation of the address.
 */
function sui_convert_public_key_to_address($pk, $flag = 0)
{
  $flag = chr($flag);

  // Prepend flag byte
  $pkBytes = $flag . $pk;

  // hash with blake2B 
  $pkHash = sodium_crypto_generichash($pkBytes, null, 32);

  return bin2hex($pkHash);
}
