<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 20:01:41
 * @@Modify Date: 2018-07-17 18:31:12
 * @@Function:
 */

namespace Magiccart\Comments\Model\System\Config\Comment;

/**
 * Used in creating options for commetns config value selection
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @const int
     */
    const DISABLED = 0;

    /**
     * @const string
     */
    const INLINE = 'inline';

    /**
     * @const string
     */
    const FACEBOOK = 'facebook';

    /**
     * @const string
     */
    const DISQUS = 'disqus';

    /**
     * @const string
     */
    const GOOGLE = 'google';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DISABLED, 'label' => __('Disabled')],
            ['value' => self::INLINE, 'label' => __('Use Inline Comments')],
            ['value' => self::FACEBOOK, 'label' => __('Use Facebook Comments')],
            ['value' => self::DISQUS, 'label' => __('Use Disqus Comments')],
            ['value' => self::GOOGLE, 'label' => __('Use Google Comments')],
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
