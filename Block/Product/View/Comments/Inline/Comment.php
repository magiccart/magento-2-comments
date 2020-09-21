<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 21:32:11
 * @@Modify Date: 2018-07-19 19:55:53
 * @@Function:
 */

namespace Magiccart\Comments\Block\Product\View\Comments\Inline;

use Magento\Store\Model\ScopeInterface;

/**
 * Magiccart comment block
 *
 * @method string getComment()
 * @method $this setComment(\Magiccart\Comments\Model\Comment $comment)
 */
class Comment extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var array
     */
    protected $repliesCollection = [];

    /**
     * Template file
     * @var string
     */
    protected $_template = 'product/view/comments/inline/comment.phtml';


    /**
     * Retrieve identities
     *
     * @return string
     */
    public function getIdentities()
    {
        return $this->getComment()->getIdentities();
    }

    public function getGravatar($email = '', $rating = 'pg')
    {
        $email = md5(strtolower(trim($email)));
        return $gravurl = "//www.gravatar.com/avatar/$email?s=50&r=$rating";
    }

    /**
     * Retrieve sub-comments collection or empty array
     *
     * @return \Magiccart\Comments\Model\ResourceModel\Comment\Collection | array
     */
    public function getRepliesCollection()
    {
        $comment = $this->getComment();
        if (!$comment->isReply()) {
            $cId = $comment->getId();
            if (!isset($this->repliesCollection[$cId])) {
                $this->repliesCollection[$cId] = $this->getComment()->getChildComments()
                    // ->addActiveFilter()
                    /*->setPageSize($this->getNumberOfReplies())*/
                    ->setOrder('created_time', 'ASC');
            }

            return $this->repliesCollection[$cId];
        } else {
            return [];
        }
    }

    /**
     * Retrieve number of replies to display
     *
     * @return string
     */
    public function getNumberOfReplies()
    {
        return $this->_scopeConfig->getValue(
            'comments/general/number_of_replies',
            ScopeInterface::SCOPE_STORE
        );
    }
}
