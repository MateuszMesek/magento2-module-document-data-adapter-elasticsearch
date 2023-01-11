<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;

class Composite implements DifferInterface
{
    private TMap $differs;

    public function __construct(
        TMapFactory $TMapFactory,
        array $differs
    )
    {
        $this->differs = $TMapFactory->createSharedObjectsMap([
            'type' => DifferInterface::class,
            'array' => $differs
        ]);
    }

    public function check(array $new, array $current): int
    {
        if (empty($current)) {
            return self::NEW;
        }

        $bitmask = self::NONE;

        foreach ($this->differs as $differ) {
            $bitmask |= $differ->check($new, $current);
        }

        if ($bitmask & self::NEW) {
            return self::NEW;
        }

        if ($bitmask & self::UPDATE) {
            return self::UPDATE;
        }

        return self::NONE;
    }
}
