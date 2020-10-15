<?php 
include_once('./classes/database.php');
include_once('./classes/transaction.php');



$reference = null;
$pesapal_tracking_id = null;

if(isset($_GET['pesapal_merchant_reference']) && isset($_GET['pesapal_transaction_tracking_id'])) {

    $reference = $_GET['pesapal_merchant_reference'];
    $pesapal_tracking_id = $_GET['pesapal_transaction_tracking_id'];

    $transaction  = new Transaction();
    $transaction->setReference($reference);
    $transaction->setTrackingId($pesapal_tracking_id);
    
    $transaction->sendToDatabase(); // Save metchant reference and tracking id to database
    
}

        


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesapal-Xero</title>
</head>
<body>
    <div style="margin:auto; max-width:200px background-color:#c1c1c1">

        <h1 id="transactionstatus">Transaction status: PENDING</h1>

        <a href="http://localhost:8080/xero/xero-php-oauth2-starter">Home</a>  
    </div>
    <script>
        var status = "PENDING";


        function getStatus(){
            if(status != "COMPLETED" || status != "FAILED"){
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                status = this.responseText;
                document.getElementById("transactionstatus").innerHTML ="Transaction status: " + status;
                }
            };
        xhttp.open("GET", "./includes/transactionstatus.inc.php?pesapal_merchant_reference=<?php echo $reference ?>&pesapal_transaction_tracking_id=<?php echo $pesapal_tracking_id?>", true);
        xhttp.send();
            setTimeout(getStatus,3000);
        }else{
            return 0;
        }
        }
        setTimeout(getStatus, 3000);
    </script> 
</body>

</html>
