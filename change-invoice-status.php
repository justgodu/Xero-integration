<?php
  ini_set('display_errors', 'On');
  require __DIR__ . '/vendor/autoload.php';
  require_once('storage.php');
  $storage = new StorageClass();
  // Use this class to deserialize error caught
  use XeroAPI\XeroPHP\AccountingObjectSerializer;
  $xeroTenantId = (string)$storage->getSession()['tenant_id'];
  $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$storage->getSession()['token'] );
  $apiInstance = new XeroAPI\XeroPHP\Api\AccountingApi(
      new GuzzleHttp\Client(),
      $config
  );
  
  $message = "no API calls";
  
 
  if(isset($_GET["invid"]) && isset($_GET['status'])){
      
      if($_GET['status'] == "paid"){
        $invoice_id = $_GET['invid'];
        

        try{
            $invoice = $apiInstance->getInvoice($xeroTenantId, $invoice_id);
        }catch(Exception $e){
            echo 'Exception when calling AccountingApi->getInvoice: ', $e->getMessage(), PHP_EOL;
        }
        
        $temp = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
       
        $accoundCode = "020";
        $invoice[0]->setAmountPaid($invoice[0]->getSubTotal()); //paid;
        $payment = new \XeroAPI\XeroPHP\Models\Accounting\Payment;
        $account = new \XeroAPI\XeroPHP\Models\Accounting\Account;
        $account->setCode($accoundCode);
        $payment->setInvoice($invoice[0])
        ->setDate(date("Y-m-d"))
        ->setAccount($account)
        ->setAmount($invoice[0]->getAmountDue());
        try {
            $result = $apiInstance->createPayment($xeroTenantId, $payment);
            echo 'PAID!';
            
        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->createPayment: ';
            // var_dump($e->getResponseBody());
            header('Location: ' . './authorizedResource.php?inv=failed&type=paid');
            exit();
        }
        header('Location: ' . './authorizedResource.php?inv=success&type=paid');
        exit();
      }
      else if($_GET['status'] == "voided"){
        $invoice_id = $_GET['invid'];
       

        try{
            $invoice = $apiInstance->getInvoice($xeroTenantId, $invoice_id);
        }catch(Exception $e){
            echo 'Exception when calling AccountingApi->getInvoice: ', $e->getMessage(), PHP_EOL;
        }
        
        $temp = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $statuses = $temp->getStatusAllowableValues();
        
        $invoice[0]->setStatus($statuses[5]); //paid status;
        
        try {
            $result = $apiInstance->updateInvoice($xeroTenantId, $invoice_id, $invoice);
            
        } catch (Exception $e) {
            echo 'Couldn\'t Void the invoice';
            // var_dump($e->getResponseBody());
            header('Location: ' . './authorizedResource.php?inv=failed&type=voided');
            exit();
        }
        
        header('Location: ' . './authorizedResource.php?inv=success&type=voided');
        exit();
      }
  }  
  
?>
