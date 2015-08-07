<?php

class ModelAccountVerification extends Model
{
    public function add($customer_id)
    {
        $verification_code = $this->getCode($customer_id);
        
        $this->db->query("INSERT INTO ".DB_PREFIX."customer_verification SET customer_id = '".(int)$customer_id."', verification_code = '".$verification_code."'");
        
        return $verification_code;
    }

    public function remove($customer_id)
    {
        $this->db->query("DELETE FROM ".DB_PREFIX."customer_verification WHERE customer_id = '".(int)$customer_id."'");
    }
    
    public function check($customer_id, $verification_code)
    {
        $customer_code = $this->db->query("SELECT verification_code FROM " . DB_PREFIX . "customer_verification WHERE customer_id='" . $customer_id . "'");
        
        return (isset($customer_code->row['verification_code']) && $customer_code->row['verification_code'] == $verification_code);
    }
    
    protected function getCode($customer_id)
    {
        return md5($customer_id . ':' . rand());
    }
}
