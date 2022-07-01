<?php
/**
 * MIT License
 *
 * Copyright (c) 2022 Front ID
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author Pedro Pelaez <pedro@front.id>
 */

// Composer autoloader.
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Twig\Extension\DebugExtension;
use GuzzleHttp\Client;

$dotenv = new Dotenv();
// loads .env, .env.local, and .env.$APP_ENV.local or .env.$APP_ENV
$dotenv->loadEnv(__DIR__.'/../.env');

// Template rendering settings.
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    // Set twig cache folder.
    'cache' => '../var/twig_cache',
    // Enable debug.
    'debug' => true,
]);
$twig->addExtension(new DebugExtension());

/**
 * Note: Next are the API call examples.
 */

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

/**
 * Note: End of API call examples.
 */

// Render page.
echo $twig->render(
    'index.html.twig',
    [
        'env' => $_ENV,
        'login_code' => $login_response->getStatusCode(),
        'login_contents' => $login_body,
        'login_meta' => $login_response->getBody()->getMetadata(),
        'trainings_code' => $trainings_response->getStatusCode(),
        'trainings_contents' => $trainings_body,
        'trainings_meta' => $trainings_response->getBody()->getMetadata(),
    ]
);
