<?php
/**
 * RequestDesk Q&A - Pair Collection
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model\ResourceModel\QaPair;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RequestDesk\Qa\Model\QaPair;
use RequestDesk\Qa\Model\ResourceModel\QaPair as QaPairResource;

/**
 * Collection of Q&A pairs.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'qa_id';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(QaPair::class, QaPairResource::class);
    }
}
