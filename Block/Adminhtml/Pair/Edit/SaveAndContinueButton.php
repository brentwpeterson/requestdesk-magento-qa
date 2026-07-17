<?php
/**
 * RequestDesk Q&A - Pair Edit Save and Continue Button
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Block\Adminhtml\Pair\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
