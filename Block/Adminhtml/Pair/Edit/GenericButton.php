<?php
/**
 * RequestDesk Q&A - Pair Edit Generic Button
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Block\Adminhtml\Pair\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Get Q&A pair ID from the request
     *
     * @return int|null
     */
    public function getQaId(): ?int
    {
        $qaId = (int)$this->context->getRequest()->getParam('qa_id');
        return $qaId ?: null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
