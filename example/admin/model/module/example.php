<?php

class ModelModuleExample extends Model
{

    public function createExampleTables()
    {
        $query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "example` (
            `example_id` int(20) NOT NULL AUTO_INCREMENT,
            `example_name` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`example_id`)
        )");
    }

    public function deleteExampleTables()
    {
        $query = $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "example");
    }

}
