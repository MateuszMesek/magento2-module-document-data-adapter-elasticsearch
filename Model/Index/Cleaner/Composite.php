<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Cleaner;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class Composite implements CleanerInterface
{
    private TMap $cleaners;

    public function __construct(
        TMapFactory $TMapFactory,
        array $cleaners
    )
    {
        $this->cleaners = $TMapFactory->createSharedObjectsMap([
            'type' => CleanerInterface::class,
            'array' => $cleaners
        ]);
    }

    public function clean(array $body): array
    {
        foreach ($this->cleaners as $cleaner) {
            /** @var \MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Cleaner\CleanerInterface $cleaner */
            $body = $cleaner->clean($body);
        }

        return $body;
    }
}
