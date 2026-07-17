<?php
/**
 * RequestDesk Q&A - Pair Model
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model;

use Magento\Framework\Model\AbstractModel;
use RequestDesk\Qa\Model\ResourceModel\QaPair as QaPairResource;

/**
 * A reusable question-and-answer pair.
 */
class QaPair extends AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(QaPairResource::class);
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return (string) $this->getData('question');
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return (string) $this->getData('answer');
    }
}
