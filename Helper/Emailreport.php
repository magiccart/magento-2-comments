<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-05-16 20:26:27
 * @@Modify Date: 2018-07-20 01:20:23
 * @@Function:
 */

namespace Magiccart\Comments\Helper;
 
use Magento\Framework\App\Area;
 
class Emailreport extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /* A constant is declared with custom field in admin created using system.xml */
 
    const XML_PATH_CUSTOM_EMAIL_TEMPLATE= 'comments/general/email_template';   // section_id/group_id/field_id
    const XML_PATH_SENDER               = 'comments/general/sender';   // section_id/group_id/field_id
    const XML_PATH_REPLYTO              = 'comments/general/replyto';   // section_id/group_id/field_id
    const XML_PATH_RECEIVER             = 'comments/general/receiver';   // section_id/group_id/field_id
 
    /**
     * Emailreport constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }
 
    /**
     * @param $path
     * @param $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
 
    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
    
    public function getSenderInfo()
    {
        /* Sender Detail */
        return  $senderInfo = [
            'name'      => $this->getStore()->getGroup()->getName(),
            'email'     => $this->getConfigValue( self::XML_PATH_SENDER, $this->getStore()->getId() ),
            'reply_to'   => $this->getConfigValue( self::XML_PATH_REPLYTO, $this->getStore()->getId() )
        ];        
    }

    public function getReceiverInfo()
    {
        /* Receiver Detail */
        return  $receiverInfo = [
            'name'   => 'receiver',
            'email'  => $this->getConfigValue( self::XML_PATH_RECEIVER, $this->getStore()->getId() )
        ];
    }
 

    /**
     * @param $template
     * @param $senderInfo
     * @param $receiverInfo
     * @param array $templateParams
     * @return $this
     */
    public function sendEmailReport(
        $template,
        $senderInfo,
        $receiverInfo,
        $templateParams = []
    ) {
        $this->inlineTranslation->suspend();
        $templateId = $this->getConfigValue($template, $this->getStore()->getStoreId());
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStore()->getId(),
                ]
            )
            ->setTemplateVars($templateParams)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name'])
            // ->setReplyTo('noreply@alothemes.com')
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
 
        return $this;
    }
}
