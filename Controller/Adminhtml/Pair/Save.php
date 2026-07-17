<?php
/**
 * RequestDesk Q&A - Pair Save Controller
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

class Save extends Action
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
     * Save a Q&A pair
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $qaId = isset($data['qa_id']) ? (int)$data['qa_id'] : null;

        try {
            $pair = $this->pairFactory->create();
            if ($qaId) {
                $this->pairResource->load($pair, $qaId);
                if (!$pair->getId()) {
                    throw new LocalizedException(__('This Q&A pair no longer exists.'));
                }
            }

            $question = trim((string)($data['question'] ?? ''));
            $answer = trim((string)($data['answer'] ?? ''));
            if ($question === '') {
                throw new LocalizedException(__('Question is required.'));
            }
            if ($answer === '') {
                throw new LocalizedException(__('Answer is required.'));
            }

            $pair->setData('question', $question);
            $pair->setData('answer', $answer);

            $this->pairResource->save($pair);

            $this->messageManager->addSuccessMessage(__('The Q&A pair has been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['qa_id' => $pair->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while saving the Q&A pair.'));
        }

        if ($qaId) {
            return $resultRedirect->setPath('*/*/edit', ['qa_id' => $qaId]);
        }
        return $resultRedirect->setPath('*/*/edit');
    }
}
