<?php
/**
 * RequestDesk Q&A - Pair Edit/New Controller
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Controller\Adminhtml\Pair;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use RequestDesk\Qa\Model\QaPairFactory;
use RequestDesk\Qa\Model\ResourceModel\QaPair as QaPairResource;

class Edit extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'RequestDesk_Qa::pairs';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QaPairFactory $pairFactory
     * @param QaPairResource $pairResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        private readonly QaPairFactory $pairFactory,
        private readonly QaPairResource $pairResource
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit or create a Q&A pair
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $qaId = (int)$this->getRequest()->getParam('qa_id');

        if ($qaId) {
            $pair = $this->pairFactory->create();
            $this->pairResource->load($pair, $qaId);
            if (!$pair->getId()) {
                $this->messageManager->addErrorMessage(__('This Q&A pair no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('RequestDesk_Qa::pairs_menu');
        $resultPage->getConfig()->getTitle()->prepend(
            $qaId ? __('Edit Q&A Pair') : __('New Q&A Pair')
        );

        return $resultPage;
    }
}
