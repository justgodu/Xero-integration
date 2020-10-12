<?php
class XeroInvoice{

    private $ID;
    private $currency;
    private $amount;
    private $shortCode;
  

    public function __construct($id, $currency, $amount, $shortCode){
        $this->ID = $id;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->shortCode = $shortCode;
    }

    public function getID(){
        return $this->ID;
    }

    public function setID($id){
        $this->ID = $id;
    }
    public function getCurrency(){
        return $this->currency;    
    }
    public function setCurrency($currency){
        $this->currency = $currency;
    }
    public function getAmount(){
        return $this->amount;
    }
    public function setAmount($amount){
        $this->amount = $amount;
    }
    public function getShortCode(){
        return $this->shortCode;
    }
    public function setShortCode($shortCode){
        $this->shortCode = $shortCode;
    }
}