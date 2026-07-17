<?php
/**
 * RequestDesk Q&A - Pair Resource Model
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for requestdesk_qa_pair.
 */
class QaPair extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init('requestdesk_qa_pair', 'qa_id');
    }
}
