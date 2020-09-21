<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 21:32:11
 * @@Modify Date: 2018-07-10 21:46:58
 * @@Function:
 */

namespace Magiccart\Comments\Block\Product\View\Comments;

use Magiccart\Comments\Model\System\Config\Comment\Type as CommetType;

/**
 * Comments post Google comments block
 */
class Google extends \Magiccart\Comments\Block\Product\View\Comments
{
    protected $commetType = CommetType::GOOGLE;
}
