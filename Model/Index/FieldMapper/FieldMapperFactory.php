<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;

class FieldMapperFactory
{
    private array $instances = [];

    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    )
    {
    }

    public function create($fieldMapper): FieldMapperInterface
    {
        $instance = match (true) {
            is_string($fieldMapper) => $this->objectManager->create($fieldMapper),
            is_callable($fieldMapper) => $this->objectManager->create(
                Callback::class,
                [
                    'callback' => $fieldMapper
                ]
            ),
            is_object($fieldMapper) => $fieldMapper,
            default => null
        };

        if (!$instance instanceof FieldMapperInterface) {
            $instanceName = match(true) {
                is_string($fieldMapper) => $fieldMapper,
                is_object($instance) => get_class($instance),
                default => null
            };

            $message = match (true) {
                null !== $instanceName => sprintf(
                    'Filed mapper "%s" must implement "%s" interface',
                    $instanceName,
                    FieldMapperInterface::class
                ),
                default => sprintf(
                    'Filed mapper must implement "%s" interface',
                    FieldMapperInterface::class
                )
            };

            throw new InvalidArgumentException($message);
        }

        return $instance;
    }

    public function get($fieldMapper): FieldMapperInterface
    {
        if (is_string($fieldMapper)) {
            if (!isset($this->instances[$fieldMapper])) {
                $this->instances[$fieldMapper] = $this->create($fieldMapper);
            }

            return $this->instances[$fieldMapper];
        }

        return $this->create($fieldMapper);
    }
}
