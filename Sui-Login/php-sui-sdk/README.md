#### example

```php
$msg =  "hello from another world!";
$sig = "AEepkHQWrKhIV8weq/J94gCPqZdCJubmN+2DRSth2zK45TCIpATYETYEUdKBWEctUsHgiiB0tw6HDOzX3LMPRQL1CpzcVhs0WP7+BGYqYUDYkSwOU2cW9NNYGiwv97RSyA==";

$res = sui_verify_personal_message($msg, $sig);

var_dump($res);
```
