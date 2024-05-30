<?php


const SIGNATURE_FLAG_TO_SCHEME = [
  0x00 => 'ED25519',
  0x01 => 'Secp256k1',
  0x02 => 'Secp256r1',
  0x03 => 'MultiSig',
  0x05 => 'ZkLogin',
];

/**
 * Parses a serialized signature and extracts its components.
 *
 * @param string $serializedSignature The base64 serialized signature string to parse.
 * @return array An associative array containing the extracted components:
 *               - "scheme": The signature scheme.
 *               - "flag": The signature scheme flag byte.
 *               - "sig": The signature bytes.
 *               - "pk": The public key bytes.
 * @throws InvalidArgumentException If the serialized signature is not a string or if the signature scheme is unsupported.
 */
function sui_parse_serialized_signature($serializedSignature)
{
  // Check if the signature is a string
  if (!is_string($serializedSignature)) {
    throw new InvalidArgumentException("Serialized signature must be a string");
  }

  $bytes = base64_decode($serializedSignature);

  // Extract flag byte
  $flag = ord($bytes[0]);  // ord converts a single-byte string to its ASCII value

  if (isset(SIGNATURE_FLAG_TO_SCHEME[$flag])) {
    $signatureScheme = SIGNATURE_FLAG_TO_SCHEME[$flag];

    // Switch statement to execute different functions based on the signature scheme
    switch ($signatureScheme) {
      case 'ED25519':

        // Extract sig bytes 
        $sigLength = 64;
        $sig = substr($bytes, 1, $sigLength);
        // Extract pk bytes 
        $pk = substr($bytes, 1 + $sigLength);
        return array(
          "flag" => $flag,
          "scheme" => $signatureScheme,
          "sig" => $sig,
          "pk" => $pk,
        );

      case 'Secp256k1':
      case 'Secp256r1':
      case 'MultiSig':
      case 'ZkLogin':
      default:
    }
  }
  throw new InvalidArgumentException("Unsupported signature scheme");
}
