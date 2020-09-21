<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 21:32:11
 * @@Modify Date: 2019-04-21 20:01:35
 * @@Function:
 */

namespace Magiccart\Comments\Block\Product\View\Comments;

use Magento\Store\Model\ScopeInterface;
use Magiccart\Comments\Model\System\Config\Comment\Type as CommetType;

/**
 * Product inline comments block
 */
class Inline extends \Magiccart\Comments\Block\Product\View\Comments
{

     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var string
     */
    protected $commetType = CommetType::INLINE;

    /**
     * @var string
     */
    protected $defaultCommentBlock = 'Magiccart\Comments\Block\Product\View\Comments\Inline\Comment';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory
     */
    protected $_commentCollectionFactory;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comment\Collection
     */
    protected $_commentCollection;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;


    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory $commentCollectionFactory
     * @param \Magiccart\Comments\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory $commentCollectionFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerUrl     = $customerUrl;
        $this->_commentCollectionFactory = $commentCollectionFactory;
        parent::__construct($context, $coreRegistry, $localeResolver, $commentCollectionFactory, $data);
    }

    /**
     * Retrieve comment block
     *
     * @return \Magiccart\Comments\Block\Product\View\Comments\Inline\Comment
     */
    public function getCommentBlock()
    {
        $k = 'comment_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getCommentBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->defaultCommentBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k);
    }

    /**
     * Retrieve comment html
     *
     * @return string
     */
    public function getCommentHtml(\Magiccart\Comments\Model\Comment $comment)
    {
        return $this->getCommentBlock()
            ->setProduct($this->getProduct())
            ->setComment($comment)
            ->toHtml();
    }

    /**
     * Prepare comments collection
     *
     * @return void
     */
    protected function prepareCommentCollection()
    {
        parent::prepareCommentCollection();

        if ($this->getPageSize()) {
            $this->_commentCollection->setPageSize($this->getPageSize());
        }
        $this->_commentCollection->setCurPage($this->getCurrentPage());
    }

    /**
     * Retrieve customer session
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Retrieve customer url model
     * @return \Magento\Customer\Model\Url
     */
    public function getCustomerUrl()
    {
        return $this->_customerUrl;
    }

    /**
     * Retrieve true if customer can post new comment or reply
     *
     * @return string
     */
    public function canPost()
    {
        return $this->_scopeConfig->getValue(
            'comments/general/guest_comments',
            ScopeInterface::SCOPE_STORE
        ) || $this->getCustomerSession()->getCustomerGroupId();
    }

    /**
     * Retrieve form url
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl('comments/comment/product');
    }
}
