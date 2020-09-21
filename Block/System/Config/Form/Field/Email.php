<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magiccart\Comments\Block\System\Config\Form\Field;

/**
 * Backend system config array field renderer
 */
class Email extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */

    protected $_developer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_developer = true;
        // $this->_developer = $context->getScopeConfig()->getValue(
        //     'comments/general/developer',
        //     \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        // );
        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {

        $this->addColumn('label', array(
            'label' => __('Label'),
            'style' => 'width:180px',
            'class' => 'label',
        ));

        $this->addColumn('email', array(
            'label' => __('Email'),
            'style' => $this->_developer ? 'width:180px;' : 'display:none;',
            'class' => 'email',
        ));


        $this->_addAfter = $this->_developer;
        $this->_addButtonLabel = __('Add \Moderator');

        parent::_construct();
    }

    public function addColumn($name, $params)
    {
        if($this->_developer) $label = $params['label'];
        else $label = ($name != 'selector') ? $params['label'] : '';
        $this->_columns[$name] = array(
            'label'     => $label, // $this->_developer ? $params['label'] : '', // empty($params['label']) ? 'Column' : $params['label'],
            'size'      => empty($params['size'])  ? false    : $params['size'],
            'style'     => empty($params['style'])  ? null    : $params['style'],
            'class'     => empty($params['class'])  ? null    : $params['class'],
            'renderer'  => false,
        );
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof \Magento\Framework\View\Element\AbstractBlock)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }
}
