# SUI PHP Utility Library

This library provides utility functions for working with SUI blockchain in PHP.

## Installation

To use this library, simply include the PHP files containing the functions in your project.

## Functions

### 1. Convert Public Key to SUI Address

Converts a public key extracted from a signature to a SUI address in hexadecimal format.

#### Function Signature

```php
sui_convert_public_key_to_address($pk, $flag = 0)
```

#### Parameters

- `$pk` (string): The public key bytes to be converted.
- `$flag` (int): The ASCII value of the flag byte to prepend to the public key. Defaults to 0x00 (ED25519).

#### Returns

- (string): The hexadecimal representation of the address.

#### Example

```php
$publicKey = "your_public_key_bytes_here";
$address = sui_convert_public_key_to_address($publicKey);
echo $address;
```

### 2. Parse Serialized Signature

Parses a serialized signature and extracts its components.

#### Function Signature

```php
sui_parse_serialized_signature($serializedSignature)
```

#### Parameters

- `$serializedSignature` (string): The base64 serialized signature string to parse.

#### Returns

- (array): An associative array containing the extracted components:
  - `"scheme"`: The signature scheme.
  - `"flag"`: The signature scheme flag byte.
  - `"sig"`: The signature bytes.
  - `"pk"`: The public key bytes.

#### Example

```php
$serializedSignature = "your_serialized_signature_here";
$components = sui_parse_serialized_signature($serializedSignature);
print_r($components);
```

### 3. Check if Address is Valid

Checks if a string is a valid SUI address, with or without `0x`.

#### Function Signature

```php
sui_is_valid_address($str)
```

#### Parameters

- `$str` (string): The SUI address.

#### Returns

- (bool): True if the address is valid, false otherwise.

#### Example

```php
$address = "your_sui_address_here";
$isValid = sui_is_valid_address($address);
echo $isValid ? "Valid address" : "Invalid address";
```

### 4. Verify Personal Message Signature

Verifies a personal message signature.

#### Function Signature

```php
sui_verify_personal_message($msg, $sig)
```

#### Parameters

- `$msg` (string): The message to verify.
- `$sig` (string): The serialized signature base64 string to verify against the message.

#### Returns

- (array): An associative array containing the following keys:
  - `"verified"`: A boolean indicating if the signature is verified.
  - `"public_key"`: The public key bytes from the signature.
  - `"address"`: SUI address of the signer as a hex.

#### Example

```php
$message = "your_message_here";
$signature = "your_serialized_signature_here";
$result = sui_verify_personal_message($message, $signature);
print_r($result);
```

## Exceptions

- `InvalidArgumentException`: Thrown by `sui_parse_serialized_signature` and `sui_verify_personal_message` if the serialized signature is invalid or the signature scheme is unsupported.

## License

This library is open-source and available under the [MIT License](LICENSE).

