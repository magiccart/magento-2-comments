<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 23:22:20
 * @@Modify Date: 2018-07-20 02:03:53
 * @@Function:
 */

namespace Magiccart\Comments\Controller\Comment;

use Magiccart\Comments\Helper\Emailreport;

class Product extends \Magiccart\Comments\Controller\Index
{

    /**
     * Core form key validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Comment model factory
     *
     * @var \Magiccart\Comments\Model\CommentFactory
     */
    protected $commentFactory;

    /**
     * Product model factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    protected $helperEmail;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magiccart\Comments\Model\CommentFactory $commentFactory
     * @param \Magiccart\Comments\Helper\Emailreport $helperEmail
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magiccart\Comments\Model\CommentFactory $commentFactory,
        Emailreport $helperEmail
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession  = $customerSession;
        $this->commentFactory   = $commentFactory;
        $this->productFactory   = $productFactory;
        $this->helperEmail      = $helperEmail;

        parent::__construct($context);
    }

    /**
     * View Comments homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();

        if (!$this->formKeyValidator->validate($request)) {
            $this->getResponse()->setRedirect(
                $this->_redirect->getRefererUrl()
            );
            return;
        }

        $comment = $this->commentFactory->create();
        $comment->setData($request->getPostValue());

        if ($this->customerSession->getCustomerGroupId()) {
            /* Customer */
            $comment->setCustomerId(
                $this->customerSession->getCustomerId()
            )->setNickname(
                $this->customerSession->getCustomer()->getName()
            )->setEmail(
                $this->customerSession->getCustomer()->getEmail()
            )->setIsAuthor(
                0
            )->setAuthorType(
                \Magiccart\Comments\Model\System\Config\AuthorType::CUSTOMER
            );
        } elseif ($this->getConfigValue(
            'comments/general/guest_comments',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            /* Guest can post review */
            $comment->setCustomerId(0)->setAuthorType(
                \Magiccart\Comments\Model\System\Config\AuthorType::GUEST
            );
        } else {
            /* Guest cannot post review */
            $this->getResponse()->setBody(json_encode([
                'success' => false,
                'message' => __('Login to submit comment'),
            ]));
            return;
        }

        /* Unset sensitive data */
        foreach (['comment_id', 'created_time', 'update_time', 'status'] as $key) {
            $comment->unsetData($key);
        }

        /* Set default status */
        $comment->setStatus(
            $this->getConfigValue(
                'comments/general/default_status',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        try {
            $product = $this->initProduct();
            if (!$product) {
                throw new \Exception(__('You cannot post comment. Comment product is not longer exist.'), 1);
            }

            $parentInfo = '';
            if ($request->getParam('parent_id')) {
                $parentComment = $this->initParentComment();
                if (!$parentComment) {
                    throw new \Exception(__('You cannot reply to this comment. Comment is not longer exist.'), 1);
                }

                if ($parentComment->getProductId() != $product->getId()
                    || $parentComment->isReply()
                ) {
                    throw new \Exception(__('You cannot reply to this comment.'), 1);
                }

                $comment->setParentId($parentComment->getId());

                $parentInfo = array(
                        'name'      => $parentComment->getNickname(),
                        'email'     => $parentComment->getEmail()
                    );
            }

            $comment->save();

            // Send notify comment to email
            $commentInfo = array(
                    // 'frontendName'     => $this->helperEmail->getStore()->getGroup()->getName(),
                    'nickname'      => $comment->getNickname(),
                    'email'         => $comment->getEmail(),
                    'content'       => $comment->getContent(),
                    'productName'   => $product->getName(),
                    'productUrl'    => $product->getProductUrl() . '#comment-' . $comment->getId(),
                );
            $receiverInfo   = $this->helperEmail->getReceiverInfo();
            $senderInfo     = $this->helperEmail->getSenderInfo();
            if($parentInfo){
                if($comment->getEmail() != $parentInfo['email']) {
                    // Send email to Customer
                    $this->helperEmail->sendEmailReport(
                        Emailreport::XML_PATH_CUSTOM_EMAIL_TEMPLATE,
                        $senderInfo,
                        $parentInfo,
                        $commentInfo
                    );                    
                }
            } 
            // Send email to admin
            if($comment->getEmail() != $receiverInfo['email']) {
                // Send email to admin
                $this->helperEmail->sendEmailReport(
                    Emailreport::XML_PATH_CUSTOM_EMAIL_TEMPLATE,
                    $senderInfo,
                    $receiverInfo,
                    $commentInfo
                );                    
            }
        } catch (\Exception $e) {
            $this->getResponse()->setBody(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
            return;
        }
        $pending = ($comment->getStatus() == \Magiccart\Comments\Model\System\Config\Comment\Status::PENDING);
        $this->getResponse()->setBody(json_encode([
            'success' => true,
            'pending' => $pending,
            'message' => $pending
                ? __('You submitted your comment for moderation.')
                : __('Thank you for your comment.')
        ]));

    }

    /**
     * Initialize and check product
     *
     * @return \Magento\Catalog\Model\Product|bool
     */
    protected function initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product_id');
        $product = $this->productFactory->create()->load($productId);
        if ($product->getStatus() != 1) {
            return false;
        }

        return $product;
    }

    /**
     * Initialize and check parent comment
     *
     * @return \Magiccart\Comments\Model\Comment|bool
     */
    protected function initParentComment()
    {
        $commentId = (int)$this->getRequest()->getParam('parent_id');

        $comment = $this->commentFactory->create()->load($commentId);
        if (!$comment->isActive()) {
            return false;
        }

        return $comment;
    }
}
