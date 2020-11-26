<?php
namespace Dblink\Dbquery\Model\ResourceModel;
class Db2ResourceModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
    $this->_init('data', 'list_id');   //here id is the primary key of custom table
    }
}