<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;

use Magento\Framework\Config\ConverterInterface;
use MateuszMesek\Framework\Config\Converter\ItemsResolver;
use MateuszMesek\Framework\Config\Converter\ProcessorInterface;

class Converter implements ConverterInterface
{
    public function __construct(
        private readonly ItemsResolver      $itemsResolver,
        private readonly ProcessorInterface $documentProcessor
    )
    {
    }

    public function convert($source): array
    {
        $data = [];

        $items = $this->itemsResolver->resolve($source, 'document');

        foreach ($items as $item) {
            $documentData = $this->documentProcessor->process($item);

            $data[$documentData['name']] = $documentData;
        }

        return $data;
    }
}
