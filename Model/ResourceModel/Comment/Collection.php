<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-01-11 23:15:05
 * @@Modify Date: 2018-07-13 16:55:51
 * @@Function:
 */

namespace Magiccart\Comments\Model\ResourceModel\Comment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Magiccart\Comments\Model\Comment', 'Magiccart\Comments\Model\ResourceModel\Comment');
    }
}
