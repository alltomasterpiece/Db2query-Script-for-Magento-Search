<?php
namespace Dblink\Dbquery\Block;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Volker\VoiceQr\Model\ResourceModel\Collection as VoiceQrCollection;
use \Volker\VoiceQr\Model\ResourceModel\CollectionFactory as VoiceQrCollectionFactory;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Search\Model\QueryFactory as DbQueryFactory;
use Dblink\Dbquery\Model\Db2ModelFactory;
use Magento\Framework\App\ResourceConnection;
use \Magento\Framework\Api\FilterBuilder;
use \Magento\Framework\Api\Search\SearchCriteriaBuilder;
use \Magento\Search\Api\SearchInterface;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
class Dbquery extends Template
{
    protected $_quote;
    protected $_registry;
    protected $_storeManager;
    protected $_urlInterface;
    protected $_checkoutSession;
    protected $_queryFactory;
    protected $_db2;
    private $_resourceConnection;
    protected $_filterBuilder;
    protected $_searchCriteriaBuilder;
    protected $_searchInterface;
    protected $_queryCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Registry $registry,
        CheckoutSession $checkoutSession,
        DbQueryFactory $queryFactory,
        Db2ModelFactory $db2,
        ResourceConnection $resourceConnection,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchInterface $searchInterface,
        QueryCollectionFactory $queryCollectionFactory,
        array $data = []
    )
    {     
        $this->_registry = $registry; 
        $this->_checkoutSession = $checkoutSession;
        $this->_quote = $this->_checkoutSession->getQuote();
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->checkoutSession = $checkoutSession;
        $this->_queryFactory = $queryFactory;
        $this->_db2 = $db2;
        $this->_resourceConnection = $resourceConnection;
        $this->_filterBuilder = $filterBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_searchInterface = $searchInterface;
        $this->_queryCollectionFactory = $queryCollectionFactory;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getCurrentCategory()
    {       
        return $this->_registry->registry('current_category');
    }

    public function getCurrentProduct()
    {       
        return $this->_registry->registry('current_product');
    }   
    public function getQuoteId()
    {
         
        return $this->_quote->getId();
    }
    public function getStoreManagerData()
    {    
        echo $this->_storeManager->getStore()->getId() . '<br />';
        
        // by default: URL_TYPE_LINK is returned
        echo $this->_storeManager->getStore()->getBaseUrl() . '<br />';        
        
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . '<br />';
        
        echo $this->_storeManager->getStore()->getUrl('product/33') . '<br />';
        
        echo $this->_storeManager->getStore()->getCurrentUrl(false) . '<br />';
            
        echo $this->_storeManager->getStore()->getBaseMediaDir() . '<br />';
            
        echo $this->_storeManager->getStore()->getBaseStaticDir() . '<br />';    
    }
    public function getSiteBaseUrl()
    {
        
        return $this->_urlInterface->getBaseUrl() ;
    } 
    
    
    public function getStoreId()
    {
       
            $storeId = $this->_storeManager->getStore()->getId();
        
        return $storeId;
    }
    
    
    
}






    
    

   

    