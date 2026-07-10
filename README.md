# NameSilo PHP API Wrapper
PHP Wrapper for the NameSilo API

## About Me
This PHP class is an API wrapper and supports all current NameSilo function as of September, 2024
(That's actually a lie, everything except for the `bidAuctions` call is supported)

This class was created for private use, but has been released under the MIT license for you to enjoy, because open-source is awesome

That said, it _should_ work, but I don't gartentee it. Open a [Issue](https://github.com/greenreader9/NameSilo-PHP-API-Wrapper/issues) or [PR](https://github.com/greenreader9/NameSilo-PHP-API-Wrapper/pulls) to fix any bugs.

## Install Me

Install via Composer:

> composer require greenreader9/namesilo-php-api

Or grab the /src/NameSiloAPI.php file, that works too

## Use Me

1. Install Me
2. Initiate Me:
~~~php
require_once __DIR__.'/vendor/autoload.php';
use Greenreader9\NameSiloAPI;

$api = new NameSiloAPI('your-api-key', 'application-name', 'bulk', 'ote');
~~~
`new NameSiloAPI($apiKey, $UserAgent, $BulkORnormal, $SandboxURL)`

`apiKey` is your NameSilo API key. Don't share with others

`UserAgent` is the name of your application. Keep it short and descriptive

`BulkORnormal` (Optional) Set to `"bulk"` to use the [BulkAPI](https://www.namesilo.com/support/v2/articles/account-options/api-automated-batch), `null` or `"normal"` to use the normal API

`SandboxURL` (Optional) Set to `false` or omit for production use. Setting to any other value will set your API call to https://`VALUE`/namesilo.com/`BULK/NORMAL`

> TIP: All commands can use either API type except the `registerDomainDrop` command, which requires the bulk API

> TIP: Sandbox mode does not work during drop/catch times, and does not work with the `registerDomainDrop` command

3. Call a function:
~~~php
$apicall = $api->listDomains();
~~~

### How to get function name?

Vist the API docs: [https://www.namesilo.com/api-reference](https://www.namesilo.com/api-reference)

Go to "Available Operations"

Find the API call you want to make and copy the part of the URL shown below

The URL shown in the docs: `https://www.namesilo.com/api/getPrices?version=1&type=xml&key=12345`

The part you copy: `getPrices` (Otherwise known as the part right after the final slash (`/`)

### How to get the function paramaters?

Vist the API docs: [https://www.namesilo.com/api-reference](https://www.namesilo.com/api-reference)

Go to "Available Operations"

Find the API call you want to make and scroll to the `Request Parameters` section

The order in which the params are listed on the API page is the order this wrapper accept them (Easy, right?)

#### To omit a paramater

Two ways you can do this:

1. Just don't send it in. If the function has 1 optional param, and no required ones, just do `$api->func()`

2. Set it to null. `$api->func('ThisIsNeeded', null, 'ThisIsAlsoNeeded')`


### What Validation is done?

Pretty much none. It does check that you used the bulkAPI for `registerDomainDrop`, and that you are not trying to call the `bidAuctions` function. It also requires that you send in all paramaters that NameSilo marks as always required.

Any other mistakes are sent to the NameSilo API, and it will (hopefully) return a helpful error message. See [API Errors Here](https://www.namesilo.com/api-reference) -> Click `Responce Codes`

## How to read the responce? 

You get the responce as a SimpleXMLElement PHP object

Example for the `checkRegisterAvailability` call:
~~~txt
object(SimpleXMLElement)#63 (3) {
  ["code"]=>
  string(3) "300"
  ["detail"]=>
  string(7) "success"
  ["available"]=>
  object(SimpleXMLElement)#61 (1) {
    ["domain"]=>
    array(17) {
      [0]=>
      string(13) "example.com"
      [1]=>
      string(13) "example.org"
    }
  }
}
~~~

And the code that made that responce:
~~~php
<?php
require_once __DIR__.'/vendor/autoload.php';
use Greenreader9\NameSiloAPI;
$api = new NameSiloAPI('xxxxxxxxxxxx', 'My Awesome Application', 'bulk');
$api_response = $api->checkRegisterAvailability('example.com,example.org');
var_dump($api->listDomains());
?>
~~~

If the NameSilo API provides additional information as XML attributes for the call, you check the documentation, or view them like:
~~~php
foreach ($api_response->available->domain as $domain){
  var_dump($domain);
}
~~~

Which will return something like:
~~~txt
object(SimpleXMLElement)#56 (2) {
  ["@attributes"]=>
  array(4) {
    ["price"]=>
    string(5) "11.05"
    ["renew"]=>
    string(5) "11.05"
    ["premium"]=>
    string(1) "0"
    ["duration"]=>
    string(2) "10"
  }
  [0]=>
  string(13) "example.com"
}
~~~

To access an attribute (Like "price" in our example), use can do:
~~~php
foreach ($api_response->available->domain as $domain){
  var_dump($domain['price']);
}
~~~

which will return something like:
~~~txt
object(SimpleXMLElement)#62 (1) {
  [0]=>
  string(5) "11.05"
}
~~~

See more about the responce for the API call you are making by reading the [NameSilo API docs](https://www.namesilo.com/api-reference)

### Get more information about your request

Most of these were used for debugging, but I left them in case you find them useful (Or if you need to debug it yourself)
~~~php
// returns last HTTP code, or NULL (If no API requests have been made)
$api->getHTTPCode();

// returns last HTTP body response, or NULL (If no API requests have been made)
$api->getLastResult();

// returns last endpoint called, or NULL (If no API requests have been made)
$api->getLastCall();

// returns last URL called, or NULL (If no API requests have been made) --WARNING:::: EXPOSES PRIVATE API KEY!!!!
// NEVER USE IN PRODUCTION!
$api->getLastURL();
~~~

## Make a PR

Obviously there is not much here in terms of code. To make a PR, just fix the bug / add the endpoint / do what you want to do, then open a PR and type the following:

1. What you did (Added the new API endpoint someEndpoint)
2. Where I can verify your work, if needed (https://www.namesilo.com/api-reference#cat/some-endpoint)
3. A programming joke (Not neceasry, but humor is generally a good thing)
4. Anything else you feel like sharing


## Get Support

I will provide support to a reasonable degree around this class. Open an [Issue](https://github.com/greenreader9/NameSilo-PHP-API-Wrapper/issues) to get support.

If your question seems more appropriate for the NameSilo team (Like adding / changing endpoints, etc), ask them instead. I only created the wrapper, not the actual API

NameSilo support can be found here: [https://www.namesilo.com/support/v2](https://www.namesilo.com/support/v2)

