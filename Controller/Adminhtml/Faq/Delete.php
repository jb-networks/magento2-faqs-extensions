<?php

/**
 *
 * @Author              Ngo Quang Cuong <bestearnmoney87@gmail.com>
 * @Date                2016-12-18 02:25:15
 * @Last modified by:   nquangcuong
 * @Last Modified time: 2016-12-24 21:05:30
 */

namespace PHPCuong\Faq\Controller\Adminhtml\Faq;

use PHPCuong\Faq\Model\ResourceModel\Faq;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'PHPCuong_Faq::faq_delete';

    /**
     * Delete Question
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        // check if we know what should be deleted
        $faq_id = $this->getRequest()->getParam('faq_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($faq_id && (int) $faq_id > 0) {
            $title = '';
            try {
                // init model and delete
                $model = $this->_objectManager->create('PHPCuong\Faq\Model\Faq');
                $model->load($faq_id);
                if ($model->getFaqId()) {

                    $title = $model->getTitle();

                    $model->delete();

                    $url_rewrite_model = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');

                    $urls_rewrite = $url_rewrite_model->getCollection()
                    ->addFieldToFilter('entity_type', Faq::FAQ_ENTITY_TYPE)
                    ->addFieldToFilter('entity_id', $faq_id)
                    ->load()->getData();

                    foreach ($urls_rewrite as $value) {
                        $url_rewrite_model = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
                        $url_rewrite_model->load($value['url_rewrite_id'])->delete();
                    }

                    $this->messageManager->addSuccess(__('The "'.$title.'" FAQ has been deleted.'));
                    return $resultRedirect->setPath('*/*/');
                }

            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['faq_id' => $faq_id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('FAQ to delete was not found.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
