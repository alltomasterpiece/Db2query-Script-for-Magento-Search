<?php
namespace Dblink\Dbquery\Model;
use Magento\Framework\Model\AbstractModel;
class Db2Model extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
    $this->_init('Dblink\Dbquery\Model\ResourceModel\Db2ResourceModel');
    }
}