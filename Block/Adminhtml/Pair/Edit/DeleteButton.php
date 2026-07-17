<?php
/**
 * RequestDesk Q&A - Pair Edit Delete Button
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Block\Adminhtml\Pair\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getQaId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this Q&A pair? It will be removed from every post and product it is attached to.'
                ) . '\', \'' . $this->getUrl('*/*/delete', ['qa_id' => $this->getQaId()]) . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
