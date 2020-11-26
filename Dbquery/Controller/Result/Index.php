<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dblink\Dbquery\Controller\Result;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\PopularSearchTerms;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;
use Dblink\Dbquery\Model\Db2ModelFactory;

/**
 * Search result.
 */
class Index extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * No results default handle.
     */
    const DEFAULT_NO_RESULT_HANDLE = 'catalogsearch_result_index_noresults';
    /**
     * Catalog session
     *
     * @var Session
     */
    protected $_catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    protected $resultFactory;

    private $_resourceConnection;
    protected $_db2;

    /**
     * @param Context $context
     * @param Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     */
    public function __construct(
        Context $context,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        ResultFactory $resultFactory,
        ResourceConnection $resourceConnection,
        Db2ModelFactory $db2
        
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->resultFactory = $resultFactory;
        $this->_resourceConnection = $resourceConnection;
        $this->_db2 = $db2;
    }

    /**
     * Display search result
     *
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
            try{   
                $post = (array) $this->getRequest()->getPost();
                $year   = $post['year'];
                $make   = $post['q'];
                $model   = $post['model'];
            } catch (\Exception $e) {
            }
        $tableName = $this->_resourceConnection->getTableName('data');
        //Initiate Connection
        $connection = $this->_resourceConnection->getConnection();
        $records = $connection->fetchAll("SELECT list_spx_black, list_garland_black, list_garland_graphite, list_garland_flo_green FROM `".$tableName."` WHERE list_model like '%".$model."%' AND list_year like '%".$year."%' AND list_make like '%".$make."%'");
         if(!empty($records)){
            for ($i = 0; $i < count($records); $i++) {
                $queryText[$i] = implode(' ', $records[$i]);
            }
            $searchQueryTextDrag = implode(' ', $queryText);
            $searchQueryText = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $searchQueryTextDrag)));
            
        }
        else {
            $searchQueryText = "";
        }
        $query = $this->_queryFactory->get();
        $query->setQueryText($searchQueryText);
        $storeId = $this->_storeManager->getStore()->getId();
        $query->setStoreId($storeId);
        $queryText = $query->getQueryText();
        if ($queryText != '') {
            $catalogSearchHelper = $this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class);
            $getAdditionalRequestParameters = $this->getRequest()->getParams();            
            unset($getAdditionalRequestParameters[QueryFactory::QUERY_VAR_NAME]);
            $handles = null;
            if ($query->getNumResults() == 0) {
                $this->_view->getPage()->initLayout();
                $handles = $this->_view->getLayout()->getUpdate()->getHandles();
                $handles[] = static::DEFAULT_NO_RESULT_HANDLE;
            }

            if (empty($getAdditionalRequestParameters) &&
                $this->_objectManager->get(PopularSearchTerms::class)->isCacheable($queryText, $storeId)
            ) {
                $this->getCacheableResult($catalogSearchHelper, $query, $handles);
            } else {
                $this->getNotCacheableResult($catalogSearchHelper, $query, $handles);
            }
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
    /**
     * Return cacheable result
     *
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
     * @param \Magento\Search\Model\Query $query
     * @param array $handles
     * @return void
     */
    private function getCacheableResult($catalogSearchHelper, $query, $handles)
    {
        if (!$catalogSearchHelper->isMinQueryLength()) {
            $redirect = $query->getRedirect();
            if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                $this->getResponse()->setRedirect($redirect);
                return;
            }
        }
        $catalogSearchHelper->checkNotes();
        $this->_view->loadLayout($handles);
        $this->_view->renderLayout();
    }

    /**
     * Return not cacheable result
     *
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
     * @param \Magento\Search\Model\Query $query
     * @param array $handles
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getNotCacheableResult($catalogSearchHelper, $query, $handles)
    {
        if ($catalogSearchHelper->isMinQueryLength()) {
            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
        } else {
            $query->saveIncrementalPopularity();
            $redirect = $query->getRedirect();
            if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                $this->getResponse()->setRedirect($redirect);
                return;
            }
        }
        $catalogSearchHelper->checkNotes();
        $this->_view->loadLayout($handles);
        $this->getResponse()->setNoCacheHeaders();
        $this->_view->renderLayout();
    }
}
