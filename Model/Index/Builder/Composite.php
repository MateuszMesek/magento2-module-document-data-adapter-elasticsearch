<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Builder;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class Composite implements BuilderInterface
{
    private TMap $builders;

    public function __construct(
        TMapFactory $TMapFactory,
        array $builders
    )
    {
        $this->builders = $TMapFactory->createSharedObjectsMap([
            'type' => BuilderInterface::class,
            'array' => $builders
        ]);
    }

    public function build(array $dimensions = []): array
    {
        $settings = [];

        foreach ($this->builders as $builder) {
            /** @var \MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Builder\BuilderInterface $builder */
            $settings[] = $builder->build($dimensions);
        }

        return array_merge_recursive(...$settings);
    }
}
