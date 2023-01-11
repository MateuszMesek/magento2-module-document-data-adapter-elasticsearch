<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config\Converter;

use DOMNode;
use MateuszMesek\Framework\Config\Converter\AttributeValueResolver;
use MateuszMesek\Framework\Config\Converter\ItemsResolver;
use MateuszMesek\Framework\Config\Converter\ProcessorInterface;

class Document implements ProcessorInterface
{
    public function __construct(
        private readonly AttributeValueResolver $attributeValueResolver,
        private readonly ItemsResolver          $itemsResolver,
        private readonly ProcessorInterface     $nodeProcessor
    )
    {
    }

    public function process(DOMNode $node): array
    {
        $data = [
            'name' => $this->attributeValueResolver->resolve($node, 'name'),
            'nodes' => []
        ];

        $items = $this->itemsResolver->resolve($node, 'node');

        foreach ($items as $item) {
            $nodeData = $this->nodeProcessor->process($item);

            $data['nodes'][$nodeData['path']] = $nodeData;
        }

        return $data;
    }
}
