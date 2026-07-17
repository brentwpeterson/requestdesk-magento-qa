<?php
/**
 * RequestDesk Q&A - Link Resolver (public API for consumers)
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model;

use Magento\Framework\App\ResourceConnection;

/**
 * The service other modules use to read and attach reusable Q&A pairs.
 * A pair (requestdesk_qa_pair) attaches to any entity through the polymorphic
 * link table (requestdesk_qa_link) keyed by entity_type + entity_id.
 */
class QaLinkResolver
{
    public const ENTITY_BLOG_POST = 'blog_post';
    public const ENTITY_PRODUCT = 'product';

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        private readonly ResourceConnection $resource
    ) {
    }

    /**
     * Reusable Q&A pairs attached to an entity, in position order.
     *
     * @param string $entityType e.g. self::ENTITY_BLOG_POST
     * @param int $entityId
     * @return array<int, array{question:string, answer:string}>
     */
    public function getPairsFor(string $entityType, int $entityId): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from(['l' => $this->resource->getTableName('requestdesk_qa_link')], [])
            ->join(
                ['p' => $this->resource->getTableName('requestdesk_qa_pair')],
                'p.qa_id = l.qa_id',
                ['question', 'answer']
            )
            ->where('l.entity_type = ?', $entityType)
            ->where('l.entity_id = ?', $entityId)
            ->order('l.position ASC');

        $pairs = [];
        foreach ($connection->fetchAll($select) as $row) {
            $pairs[] = [
                'question' => (string) $row['question'],
                'answer' => (string) $row['answer'],
            ];
        }
        return $pairs;
    }

    /**
     * Attach an existing pair to an entity (idempotent).
     *
     * @param int $qaId
     * @param string $entityType
     * @param int $entityId
     * @param int $position
     * @return void
     */
    public function attach(int $qaId, string $entityType, int $entityId, int $position = 0): void
    {
        $connection = $this->resource->getConnection();
        $connection->insertOnDuplicate(
            $this->resource->getTableName('requestdesk_qa_link'),
            [
                'qa_id' => $qaId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'position' => $position,
            ],
            ['position']
        );
    }

    /**
     * Detach a pair from an entity.
     *
     * @param int $qaId
     * @param string $entityType
     * @param int $entityId
     * @return void
     */
    public function detach(int $qaId, string $entityType, int $entityId): void
    {
        $connection = $this->resource->getConnection();
        $connection->delete(
            $this->resource->getTableName('requestdesk_qa_link'),
            [
                'qa_id = ?' => $qaId,
                'entity_type = ?' => $entityType,
                'entity_id = ?' => $entityId,
            ]
        );
    }

    /**
     * Q&A pair ids attached to an entity, in position order.
     *
     * @param string $entityType
     * @param int $entityId
     * @return int[]
     */
    public function getQaIdsFor(string $entityType, int $entityId): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($this->resource->getTableName('requestdesk_qa_link'), ['qa_id'])
            ->where('entity_type = ?', $entityType)
            ->where('entity_id = ?', $entityId)
            ->order('position ASC');
        return array_map('intval', $connection->fetchCol($select));
    }

    /**
     * Replace an entity's Q&A links with exactly the given set, in order.
     *
     * @param string $entityType
     * @param int $entityId
     * @param int[] $qaIds
     * @return void
     */
    public function syncForEntity(string $entityType, int $entityId, array $qaIds): void
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('requestdesk_qa_link');
        $connection->delete($table, [
            'entity_type = ?' => $entityType,
            'entity_id = ?' => $entityId,
        ]);

        $rows = [];
        $position = 0;
        foreach (array_unique(array_filter(array_map('intval', $qaIds))) as $qaId) {
            $rows[] = [
                'qa_id' => $qaId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'position' => $position++,
            ];
        }
        if ($rows !== []) {
            $connection->insertMultiple($table, $rows);
        }
    }
}
