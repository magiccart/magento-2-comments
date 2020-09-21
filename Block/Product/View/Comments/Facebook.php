<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 21:32:11
 * @@Modify Date: 2018-07-19 00:47:24
 * @@Function:
 */
namespace Magiccart\Comments\Block\Product\View\Comments;

use Magiccart\Comments\Model\System\Config\Comment\Type as CommetType;

/**
 * Comments post Facebook comments block
 */
class Facebook extends \Magiccart\Comments\Block\Product\View\Comments
{
    protected $commetType = CommetType::FACEBOOK;
}
