<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Cleaner;

interface CleanerInterface
{
    public function clean(array $body): array;
}
