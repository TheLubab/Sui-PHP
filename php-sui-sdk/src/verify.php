<?php



/**
 * Verifies a personal message signature.
 *
 * @param string $msg The message to verify.
 * @param string $sig The serialized signature base64 string to verify against the message. 
 *
 * @return array An associative array containing the following keys:
 *                     - "verified": A boolean indicating if the signature is verified 
 *                     - "public_key": The public key bytes from the signature 
 *                     - "address": SUI address of the signer as a hex
 *
 * @throws InvalidArgumentException If the serialized signature is invalid or the signature scheme is unsupported.
 */
function sui_verify_personal_message($msg, $sig)
{
  $parts = sui_parse_serialized_signature($sig);

  $flag = $parts["flag"];
  $sig = $parts["sig"];
  $pk = $parts["pk"];

  // add length as first byte (https://github.com/MystenLabs/sui/blob/afeb315e5473232aaf7bd26244980af1d184885a/sdk/typescript/src/cryptography/publickey.ts#L86)
  $msg = chr(strlen($msg)) . $msg;

  // add intent (https://github.com/MystenLabs/sui/blob/afeb315e5473232aaf7bd26244980af1d184885a/sdk/typescript/src/cryptography/intent.ts#L29)
  $msg = pack('C', 3) . pack('C', 0) . pack('C', 0) . $msg;

  // hash with blake2B (https://github.com/MystenLabs/sui/blob/afeb315e5473232aaf7bd26244980af1d184885a/sdk/typescript/src/cryptography/publickey.ts#L73)
  $msg = sodium_crypto_generichash($msg, null, 32);

  $verified = sodium_crypto_sign_verify_detached($sig, $msg, $pk);

  return [
    "verified" => !!$verified,
    "public_key" => $pk,
    "address" => sui_convert_public_key_to_address($pk, $flag),
  ];
}
