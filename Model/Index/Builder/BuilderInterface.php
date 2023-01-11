<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Builder;

interface BuilderInterface
{
    public function build(array $dimensions = []): array;
}
