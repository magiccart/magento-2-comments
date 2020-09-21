<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 19:01:36
 * @@Modify Date: 2018-07-10 19:50:26
 * @@Function:
 */

namespace Magiccart\Comments\Block;

use Magento\Store\Model\ScopeInterface;

// class Comments extends Magento\Catalog\Block\Product\View\abstractView
class Comments extends \Magento\Framework\View\Element\Template
{

    protected $_scopeConfig;
 
     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;


    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_sessionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comments\CollectionFactory
     */
    protected $_commentCollectionFactory;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comments\Collection
     */
    protected $_commentCollection;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Customer\Model\SessionFactory $sessionFactory
     * @param \Magiccart\Comments\Model\ResourceModel\Post\CollectionFactory $commentCollectionFactory
     * @param \Magiccart\Comments\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magiccart\Comments\Model\ResourceModel\Comments\CollectionFactory $commentCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig     = $context->getScopeConfig();
        $this->_coreRegistry    = $coreRegistry;
        $this->_filterProvider  = $filterProvider;
        $this->_sessionFactory  = $sessionFactory;
        $this->_commentCollectionFactory = $commentCollectionFactory;
        $this->_url = $context->getUrlBuilder();
    }

    /**
     * Prepare comments collection
     *
     * @return void
     */
    protected function _prepareCommentCollection()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $productId = $this->_coreRegistry->registry('current_product')->getId();
        $this->_commentCollection = $this->_commentCollectionFactory->create()
                                    ->addFieldToFilter('product_id',  $productId)
                                    ->addFieldToFilter('store',  $store)
                                    ->addFieldToFilter('status', 1)
                                    ->setOrder('comment_id', 'DESC');

        if ($this->getPageSize()) {
            $this->_commentCollection->setPageSize($this->getPageSize());
        }
        $this->_commentCollection->setCurPage($this->getCurrentPage());
    }

    /**
     * Prepare comments collection
     *
     * @return \Magiccart\Comments\Model\ResourceModel\Comments\Collection
     */
    public function getCommentsCollection()
    {
        if (null === $this->_commentCollection) {
            $this->_prepareCommentCollection();
        }
        return $this->_commentCollection;
    }

    public function getCurrentPage()
    {
        $page = (int) $this->getRequest()->getParam('p', 1);
        return $page ? $page : 1;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue(
            'comments/general/enabled',
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }

        return parent::_toHtml();
    }

}
