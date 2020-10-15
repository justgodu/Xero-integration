<?php
  ini_set('display_errors', 'On');
  require __DIR__ . '/vendor/autoload.php';
  require_once('storage.php');

  // Use this class to deserialize error caught
  use XeroAPI\XeroPHP\AccountingObjectSerializer;

  // Storage Classe uses sessions for storing token > extend to your DB of choice
  $storage = new StorageClass();

    $xeroTenantId = (string)$storage->getSession()['tenant_id'];
 
  if ($storage->getHasExpired()) {
    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => '07885311897144579D1F0C7570738FB2',
      'clientSecret'            => 'yel0kT2KMChkJO4CxgXQa__jpVWkWMkG1WcV8KKG3jjozR0U',
      'redirectUri'             => 'http://localhost:8080/xero/xero-php-oauth2-starter/callback.php',
      'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
      'urlAccessToken'          => 'https://identity.xero.com/connect/token',
      'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
    ]);

    $newAccessToken = $provider->getAccessToken('refresh_token', [
      'refresh_token' => $storage->getRefreshToken()
    ]);

    // Save my token, expiration and refresh token
    $storage->setToken(
        $newAccessToken->getToken(),
        $newAccessToken->getExpires(),
        $xeroTenantId,
        $newAccessToken->getRefreshToken(),
        $newAccessToken->getValues()["id_token"]);
  }

  $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$storage->getSession()['token'] );
  $apiInstance = new XeroAPI\XeroPHP\Api\AccountingApi(
      new GuzzleHttp\Client(),
      $config
  );
  
  $message = "no API calls";
//   if(isset($_GET['inv']) && isset($_GET['type'])){
//       if($_GET['type'] == "paid")
//       $message = $_GET['inv'] == "success" ? "Successfully Paid" : "Payment Failed";
//       if($_GET['type'] == "voided") 
//       $message = $_GET['inv'] == "success" ? "Successfully Voided" : "Deletion Failed";
//   }
  if(isset($_GET['invoiceNo']) && isset($_GET['currency']) && isset($_GET['amount']) && isset($_GET['shortCode'])){
    require_once('invoice.php'); 
    $xeroInvoice = new XeroInvoice($_GET['invoiceNo'], $_GET['currency'], $_GET['amount'], $_GET['shortCode']);
    
}
  if (isset($_GET['action'])) {
    if($_GET["action"]==10){

        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactId("2237351e-0530-4820-8624-cc4428cdf764"); //ID of xero contact
        $lineItem = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
        $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $lineItem->setDescription("New Demo Invoice")->setQuantity(1)
        ->setUnitAmount(1.0)
        ->setAccountCode('200')//Xero account id
        ->setTaxType("NONE")
        ->setLineAmount('1');
        $lineItems =  [];
        array_push($lineItems, $lineItem);
        $date = new \DateTime("2020-10-08T22:20:30+01:00");
        $dueDate = new \DateTime("2019-10-08T22:20:30+01:00");
        $reference = "Some reference";
        $status = $invoice->getStatusAllowableValues()[3];// Authorized status

 

        
        $invoice->setType("ACCREC")
        ->setContact($contact)
        ->setLineItems($lineItems)
        ->setDate($date)
        ->setDueDate($dueDate)
        ->setReference($reference)
        ->setStatus($status)
        ->setCurrencyCode("KES");
        
        
        $request_empty =  new \XeroAPI\XeroPHP\Models\Accounting\RequestEmpty;
        
        
        $summarize_errors = true;
        try{
            $invoice = $apiInstance->createInvoices($xeroTenantId, $invoice, $summarize_errors);
            $invoice_id = $invoice[0]->getInvoiceId();
            
            $apiInstance->emailInvoice($xeroTenantId, $invoice_id, $request_empty);
            $message = "Please check you email"; 
        }catch (Exception $e){
            echo 'Exception when calling AccountingApi->createInvoices: ', $e->getMessage(), PHP_EOL;
            print_r($e->getResponseBody());
        }

    }
  }
?>
<html>
    <body>
        <ul>
            
            <?php if(isset($xeroInvoice)){ 
                    $fname = "demo";
                    $lname = "demo";
                    $email = "demo@demo.com";
                ?>
                <li><a href="pesapal-iframe.php?invoiceNo=<?php echo $xeroInvoice->getID()?>&fname=<?php echo $fname ?>&lname=<?php echo $fname ?>&email=<?php echo $email ?>">Pay Invoice</a></li>
                <li><a href="change-invoice-status.php?invid=<?php echo $xeroInvoice->getID()?>&status=voided">Void Invoice</a></li>
            <?php }else{?>
                <!-- <li><a href="authorizedResource.php?action=1">Get Organisation Name</a></li> -->
                <li><a href="authorizedResource.php?action=10">Generate invoice and pay</a></li>
            
            <?php }?>
            
        </ul>
        <div>
        <?php
            echo($message );
        ?>
        </div>
    </body>
</html>