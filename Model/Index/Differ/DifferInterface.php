<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ;

interface DifferInterface
{
    public const NONE = 0;
    public const NEW = 1;
    public const UPDATE = 2;

    public function check(array $new, array $current): int;
}
