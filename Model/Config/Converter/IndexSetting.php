<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config\Converter;

use DOMNode;
use MateuszMesek\Framework\Config\Converter\AttributeValueResolver;
use MateuszMesek\Framework\Config\Converter\ProcessorInterface;

class IndexSetting implements ProcessorInterface
{
    public function __construct(
        private readonly AttributeValueResolver $attributeValueResolver
    )
    {
    }

    public function process(DOMNode $node): array
    {
        return [
            'path' => $this->attributeValueResolver->resolve($node, 'path'),
            'type' => $this->attributeValueResolver->resolve($node, 'type')
        ];
    }
}
