<?php

class ModelModuleSingleclick extends Model
{

    public function add($data)
    {
        $time = time();
        $sql = "INSERT INTO " . DB_PREFIX . "singleclick "
            . "SET name = '" . $data['name'] . "', phone = '" . $data['phone'] . "', message='" . $data['message'] . "', date='" . $time . "'";
        $query = $this->db->query($sql);
    }

}
