<?php
ini_set('register_globals', 'Off');
ini_set('display_errors', 'On');
require __DIR__ . '/vendor/autoload.php';
require_once('storage.php');

// Storage Classe uses sessions for storing token > extend to your DB of choice
$storage = new StorageClass();

  $provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '07885311897144579D1F0C7570738FB2',
    'clientSecret'            => 'yel0kT2KMChkJO4CxgXQa__jpVWkWMkG1WcV8KKG3jjozR0U',
    'redirectUri'             => 'http://localhost:8080/xero/xero-php-oauth2-starter/callback.php',
    'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
    'urlAccessToken'          => 'https://identity.xero.com/connect/token',
    'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
  ]);
  print_r($_SESSION);


  // If we don't have an authorization code then get one
  if (!isset($_GET['code'])) {
    echo "Something went wrong, no authorization code found";
    exit("Something went wrong, no authorization code found");

    
  // Check given state against previously stored one to mitigate CSRF attack
  } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    echo "Invalid State";
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
  } else {

    try {
      // Try to get an access token using the authorization code grant.
      $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
      ]);

      $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
      $identityInstance = new XeroAPI\XeroPHP\Api\IdentityApi(
        new GuzzleHttp\Client(),
        $config
      );

      $result = $identityInstance->getConnections();

      // Save my tokens, expiration tenant_id
      $storage->setToken(
          $accessToken->getToken(),
          $accessToken->getExpires(),
          $result[0]->getTenantId(),
          $accessToken->getRefreshToken(),
          $accessToken->getValues()["id_token"]
      );

      header('Location: ' . './authorizedResource.php');
      exit();

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
      echo "Callback failed";
      exit();
    }
  }

?>