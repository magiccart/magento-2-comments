<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-01-11 23:15:05
 * @@Modify Date: 2018-07-19 16:52:55
 * @@Function:
 */

namespace Magiccart\Comments\Model;

class Comment extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Product model factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $author;

    /**
     * @var \Magiccart\Comments\Model\ResourceModel\Comment\Collection
     */
    protected $comments;

    protected $_commentCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magiccart\Comments\Model\ResourceModel\Comment\CollectionFactory $commentCollectionFactory,
        \Magiccart\Comments\Model\ResourceModel\Comment $resource,
        \Magiccart\Comments\Model\ResourceModel\Comment\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->productFactory = $productFactory;
        $this->_commentCollectionFactory = $commentCollectionFactory;
    }


    /**
     * Retrieve product
     * @return \Magento\Catalog\Model\Product | false
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', false);
            if ($productId = $this->getData('product_id')) {
                $product = $this->productFactory->create()->load($productId);
                if ($product->getId()) {
                    $this->setData('product', $product);
                }
            }
        }

        return $this->getData('product');
    }

    /**
     * Retrieve author
     * @return \\Magento\Framework\DataObject
     */
    public function getAuthor()
    {
        if (null === $this->author) {
            $this->author = new \Magento\Framework\DataObject;
            $this->author->setType(
                $this->getAuthorType()
            );

            switch ($this->getAuthorType()) {
                case \Magiccart\Comments\Model\System\Config\AuthorType::GUEST:
                    $this->author->setData([
                        'nickname' => $this->getNickname(),
                        'email' => $this->getEmail(),
                    ]);
                    break;
                case \Magiccart\Comments\Model\System\Config\AuthorType::CUSTOMER:
                    $customer = $this->customerFactory->create();
                    $customer->load($this->getCustomerId());
                    $this->author->setData([
                        'nickname' => $customer->getName(),
                        'email' => $this->getEmail(),
                        'customer' => $customer,
                    ]);
                    break;
                case \Magiccart\Comments\Model\System\Config\AuthorType::ADMIN:
                    $admin = $this->userFactory->create();
                    $admin->load($this->getAdminId());
                    $this->author->setData([
                        'nickname' => $customer->getName(),
                        'email' => $this->getEmail(),
                        'admin' => $admin,
                    ]);
                    break;
            }
        }

        return $this->author;
    }

    /**
     * Retrieve child comments
     * @return \Magiccart\Comments\Model\ResourceModel\Comment\Collection
     */
    public function getChildComments()
    {
        if (is_null($this->comments)) {
            $this->comments = $this->_commentCollectionFactory->create()
                ->addFieldToFilter('parent_id', $this->getId());
        }

        return $this->comments;
    }

    /**
     * Retrieve true if post is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == \Magiccart\Comments\Model\System\Config\Comment\Status::APPROVED);
    }

    /**
     * Retrieve true if comment is reply to other comment
     * @return boolean
     */
    public function isReply()
    {
        return (bool)$this->getParentId();
    }

    /**
     * Retrieve post publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Magiccart\Comments\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('created_time')
        );
    }

}
