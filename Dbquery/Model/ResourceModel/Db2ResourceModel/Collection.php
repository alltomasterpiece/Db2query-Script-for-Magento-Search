<?php
namespace Dblink\Dbquery\Model\ResourceModel\Db2ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
    $this->_init(
        'Dblink\Dbquery\Model\Db2Model',
        'Dblink\Dbquery\Model\ResourceModel\Db2ResourceModel'
    );

    }
}
