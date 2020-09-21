<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-01-05 10:40:51
 * @@Modify Date: 2018-07-13 17:41:09
 * @@Function:
 */

namespace Magiccart\Comments\Controller\Adminhtml\Index;

class MassStatus extends \Magiccart\Comments\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $commentIds = $this->getRequest()->getParam('comment');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');
        if (!is_array($commentIds) || empty($commentIds)) {
            $this->messageManager->addError(__('Please select Comments(s).'));
        } else {
            $collection = $this->_commentsCollectionFactory->create()
                // ->setStoreViewId($storeViewId)
                ->addFieldToFilter('comment_id', ['in' => $commentIds]);
            try {
                foreach ($collection as $item) {
                    $item->setStoreViewId($storeViewId)
                        ->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($commentIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
