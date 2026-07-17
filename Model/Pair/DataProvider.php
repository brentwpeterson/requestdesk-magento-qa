<?php
/**
 * RequestDesk Q&A - Pair Form Data Provider
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model\Pair;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use RequestDesk\Qa\Model\ResourceModel\QaPair\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $pair) {
            $this->loadedData[(int)$pair->getId()] = $pair->getData();
        }

        $data = $this->dataPersistor->get('requestdesk_qa_pair');
        if (!empty($data)) {
            $pair = $this->collection->getNewEmptyItem();
            $pair->setData($data);
            $this->loadedData[$pair->getId()] = $pair->getData();
            $this->dataPersistor->clear('requestdesk_qa_pair');
        }

        return $this->loadedData ?? [];
    }
}
