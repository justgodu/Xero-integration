<?php 
include_once('../classes/database.php');
require_once("../classes/transaction.php");
$reference = null;
$pesapal_tracking_id = null;

if(isset($_GET['pesapal_merchant_reference']) && isset($_GET['pesapal_transaction_tracking_id']) && $_GET['pesapal_transaction_tracking_id']!=""  && $_GET['pesapal_merchant_reference']!= "") {

    $reference = $_GET['pesapal_merchant_reference'];
    $pesapal_tracking_id = $_GET['pesapal_transaction_tracking_id'];
    $transaction  = new Transaction();
    $transaction->setReference($reference);
    $transaction->setTrackingId($pesapal_tracking_id);

    echo $transaction->getStatusFromDatabase();

}else{
    echo "Error attributes not set";
}


?>