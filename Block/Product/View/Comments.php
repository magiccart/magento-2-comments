<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 19:01:36
 * @@Modify Date: 2018-07-19 19:57:07
 * @@Function:
 */

namespace Magiccart\Comments\Block\Product\View;

use Magento\Store\Model\ScopeInterface;
use Magiccart\Comments\Model\System\Config\Comment\Type as CommetType;

// class Comments extends Magento\Catalog\Block\Product\View\abstractView
class Comments extends \Magento\Framework\View\Element\Template
{

    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $commetType;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comment\Collection
     */
    protected $_commentCollection;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory
     */
    protected $_commentCollectionFactory;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\Locale\ResolverInterface      $localeResolver
     * @param \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory $commentCollectionFactory
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory $commentCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_localeResolver = $localeResolver;
        $this->_scopeConfig     = $context->getScopeConfig();
        $this->_commentCollectionFactory = $commentCollectionFactory;

        $this->setTabTitle();
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        if( $this->getCommentsType() != CommetType::INLINE ){
            $title = __('Comments');
        } else {
            $collection = $this->getCommentsCollection();
            $title = $collection
                ? __('Comments %1', '<span class="counter">' . $collection->getSize() . '</span>')
                : __('Comments');
        }
        $this->setTitle($title);
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Prepare comments collection
     *
     * @return void
     */
    protected function prepareCommentCollection()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $productId = $this->getProduct()->getId();
        $this->_commentCollection = $this->_commentCollectionFactory->create()
                                    ->addFieldToFilter('product_id',  $productId)
                                    ->addFieldToFilter('parent_id',  0)
                                    // ->addFieldToFilter('store',  $store)
                                    ->addFieldToFilter('store',array( array('finset' => 0), array('finset' => $store)))
                                    ->addFieldToFilter('status', 1)
                                    ->setOrder('comment_id', 'DESC');
    }

    /**
     * Prepare comments collection
     *
     * @return \Magiccart\Comments\Model\ResourceModel\Comments\Collection
     */
    public function getCommentsCollection()
    {
        if (null === $this->_commentCollection) {
            $this->prepareCommentCollection();
        }
        return $this->_commentCollection;
    }

    /**
     * Retrieve comments type
     * @return bool
     */
    public function getCommentsType()
    {
        return $this->_scopeConfig->getValue(
            'comments/general/type',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve number of comments to display
     * @return int
     */
    public function getNumberOfComments()
    {
        return (int)$this->_scopeConfig->getValue(
            'comments/general/number_of_comments',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve facebook app id
     * @return string
     */
    public function getFacebookAppId()
    {
        return $this->_scopeConfig->getValue(
            'comments/general/fb_app_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve disqus forum shortname
     * @return string
     */
    public function getDisqusShortname()
    {
        return $this->_scopeConfig->getValue(
            'comments/general/disqus_forum_shortname',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getGravatar($email = '', $rating = 'pg', $size=60)
    {
        $email = md5(strtolower(trim($email)));
        return $gravurl = "//www.gravatar.com/avatar/$email?s=$size&r=$rating";
    }

    /**
     * Retrieve locale code
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->_localeResolver->getLocale();
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
        if ($this->commetType && $this->commetType != $this->getCommentsType()) {
            return '';
        }
        return parent::_toHtml();
    }

}
