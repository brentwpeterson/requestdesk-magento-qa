<?php
/**
 * RequestDesk Q&A - Pair Delete Controller
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Controller\Adminhtml\Pair;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use RequestDesk\Qa\Model\QaPairFactory;
use RequestDesk\Qa\Model\ResourceModel\QaPair as QaPairResource;

class Delete extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'RequestDesk_Qa::pairs';

    /**
     * @param Context $context
     * @param QaPairFactory $pairFactory
     * @param QaPairResource $pairResource
     */
    public function __construct(
        Context $context,
        private readonly QaPairFactory $pairFactory,
        private readonly QaPairResource $pairResource
    ) {
        parent::__construct($context);
    }

    /**
     * Delete a Q&A pair. Its entity links cascade via FK
     * (requestdesk_qa_link.qa_id onDelete CASCADE).
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $qaId = (int)$this->getRequest()->getParam('qa_id');

        if (!$qaId) {
            $this->messageManager->addErrorMessage(__('Q&A pair ID is required.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $pair = $this->pairFactory->create();
            $this->pairResource->load($pair, $qaId);
            if (!$pair->getId()) {
                $this->messageManager->addErrorMessage(__('This Q&A pair no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $this->pairResource->delete($pair);
            $this->messageManager->addSuccessMessage(__('The Q&A pair has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting the Q&A pair.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
