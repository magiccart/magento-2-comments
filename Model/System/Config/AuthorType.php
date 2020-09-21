<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 23:39:54
 * @@Modify Date: 2018-07-13 00:28:09
 * @@Function:
 */
namespace Magiccart\Comments\Model\System\Config;

/**
 * Authors types
 *
 */
class AuthorType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @const string
     */
    const GUEST = 0;

    /**
     * @const string
     */
    const CUSTOMER = 1;

    /**
     * @const string
     */
    const ADMIN = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GUEST, 'label' => __('Guest')],
            ['value' => self::CUSTOMER, 'label' => __('Customer')],
            ['value' => self::ADMIN, 'label' => __('Admin')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
