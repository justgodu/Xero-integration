<?php
class Transaction extends Database{
    private $reference;
    private $trackingId;
    public function __construct(){
        
        $this->connect();
    }
    


    public function getReference(){
        return $this->reference;
    }

    public function setReference($reference){
        $this->reference = $reference;
    }
    public function getTrackingId(){
        return $this->trackingId;
    }

    public function setTrackingId($trackingId){
        $this->trackingId = $trackingId;
    }

    public function sendToDatabase(){
        try{
        $sql = "INSERT INTO `transaction` (`orderId`, `trackingId`) VALUES ('". $this->reference . "', '". $this->trackingId."')";
        echo $sql;
        }catch(Exception $ex){
            return 'Error creating sql statement: ' . $ex->getMessage() . PHP_EQL; 
            // return false;
        }
        if($this->conn->query($sql) === TRUE){
            return "true";
        }else{
            return $this->conn;
        }

        
    }

    public function updateTransactionStatus($status){
        try{
            
            $sql = "UPDATE SET orderStatus = \'". $status."'";
            }catch(Exception $ex){
                // return 'Error creating sql statement: ' . $ex->getMessage() . PHP_EQL; 
                return false;
            }
            if($this->conn->query($sql) === TRUE){
                return true;
            }else{
                
                return false;
            }
    }
    public function getStatusFromDatabase(){
        try{
            $sql = "SELECT * FROM `transaction` WHERE `orderId` = '". $this->reference ."'";
            $result = $this->conn->query($sql);
            }catch(Exception $ex){
                return 'Error creating sql statement: ' . $ex->getMessage() . PHP_EQL; 
                
            }
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    return $row['orderStatus'];
                }
                
            }else{
                return "false";
            }
    }
}