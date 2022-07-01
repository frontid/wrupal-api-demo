# wrupal-api-demo
Wrupal API demo client.

## Install

This demo requires PHP 7.2.5 or superior with `ext-json` installed, and `composer` package manager.

To install execute:

`composer install`

Then copy `.env` from this root folder to `.env.local`. Edit the new file and enter your credentials:

```dotenv
WRUPAL_PATH="<PATH TO YOUR WRUPAL INSTANCE>"
WRUPAL_USER="<API USER PROVIDED>"
WRUPAL_PASS="<API USER PASSWORD>"
WRUPAL_SECRET="<SECRET GENERATED TO YOUR INSTANCE>"
```

Then open your browser to `[your server URL]/public/index.php`.

## Demo code

You can find the demo code in `/public/index.php` in this repository.

```php
// Get remote token.
$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => $_ENV["WRUPAL_PATH"],
]);
/**
curl --location --request POST 'https://wrupal.localhost/oauth2/token' \
--form 'grant_type="password"' \
--form 'username="user"' \
--form 'password="pass"' \
--form 'client_secret="secret"' \
--form 'client_id="woocommerce"' \
--form 'scope="scope_commerce"'
 */
$login_response = $client->post(
    '/oauth2/token',
    [
        'form_params' => [
            'grant_type' => 'password',
            'username' => $_ENV["WRUPAL_USER"],
            'password' => $_ENV["WRUPAL_PASS"],
            'client_secret' => $_ENV["WRUPAL_SECRET"],
            'client_id' => $_ENV["WRUPAL_CLIENT_ID"],
            'scope' => $_ENV["WRUPAL_SCOPE"],
        ]
    ]
);
$login_body = new \stdClass();
if ($login_response->getStatusCode() === 200) {
    $login_body = json_decode($login_response->getBody()->getContents(), true);
}

// Get trainings.
/**
curl --location --request GET 'http://wrupal.localhost/api/v1/commerce/learning_paths' \
--header 'Authorization: Bearer 2e610b47cce426c3942c9112361f35cf93421854'
 */
$trainings_response = $client->get(
    '/api/v1/commerce/learning_paths',
    [
        'headers' => [
            'Authorization' => 'Bearer '.$login_body["access_token"],
        ]
    ]
);
$trainings_body = new \stdClass();
if ($trainings_response->getStatusCode() === 200) {
    $trainings_body = json_decode($trainings_response->getBody()->getContents(), true);
}
```

## Example output

# Wrupal API client demo
Current environment settings:

```
array(8) {
    ["WRUPAL_PATH"]=> string(31) "https://demo.edu.wrupal.com"
    ["WRUPAL_USER"]=> string(3) "user"
    ["WRUPAL_PASS"]=> string(16) "pass"
    ["WRUPAL_SECRET"]=> string(32) "secret"
    ["WRUPAL_CLIENT_ID"]=> string(11) "woocommerce"
    ["WRUPAL_SCOPE"]=> string(14) "scope_commerce"
    ["SYMFONY_DOTENV_VARS"]=> string(87) "WRUPAL_PATH,WRUPAL_USER,WRUPAL_PASS,WRUPAL_SECRET,WRUPAL_CLIENT_ID,WRUPAL_SCOPE,APP_ENV"
    ["APP_ENV"]=> string(3) "dev"
}
```

## Request login:

### Code

int(200)

### Contents

```
array(5) {
    ["access_token"]=> string(40) "f0e96c043a624204ad21f8fad908fd5b5502ba9c"
    ["expires_in"]=> int(3600)
    ["token_type"]=> string(6) "Bearer"
    ["scope"]=> string(14) "scope_commerce"
    ["refresh_token"]=> string(40) "b13d24c628ec251d9cf370d20e90e9429df56ade"
}
```

### Meta

```
array(6) {
    ["wrapper_type"]=> string(3) "PHP"
    ["stream_type"]=> string(4) "TEMP"
    ["mode"]=> string(3) "w+b"
    ["unread_bytes"]=> int(0)
    ["seekable"]=> bool(true)
    ["uri"]=> string(10) "php://temp"
}
```

## Request trainings:

### Code

int(200)

## Contents

```
array(1) {
    ["trainings"]=>
    array(1) {
    [0]=>
    array(8) {
    ["id"]=>
    string(2) "26"
    ["uuid"]=>
    string(36) "c9adf09c-2754-4f02-889e-9d5fe029a0c7"
    ["title"]=>
    string(4) "Demo"
    ["description"]=>
    string(0) ""
    ["price"]=>
    string(1) "0"
    ["classes"]=>
    array(0) {
    }
    ["image"]=>
    array(1) {
    ["url"]=>
    string(83) "https://demo.edu.wrupal.com/modules/contrib/opigno_catalog/img/img_training.png"
    }
    ["visibility"]=>
    string(7) "private"
    }
    }
}
```

### Meta

```
array(6) {
    ["wrapper_type"]=> string(3) "PHP"
    ["stream_type"]=> string(4) "TEMP"
    ["mode"]=> string(3) "w+b"
    ["unread_bytes"]=> int(0)
    ["seekable"]=> bool(true)
    ["uri"]=> string(10) "php://temp"
}
```
