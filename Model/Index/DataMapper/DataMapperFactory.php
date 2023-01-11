<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\DataMapper;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;

class DataMapperFactory
{
    private array $instances = [];

    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    )
    {
    }

    public function create($dataMapper): DataMapperInterface
    {
        $instance = match (true) {
            is_string($dataMapper) => $this->objectManager->create($dataMapper),
            is_callable($dataMapper) => $this->objectManager->create(
                Callback::class,
                [
                    'callback' => $dataMapper
                ]
            ),
            is_object($dataMapper) => $dataMapper,
            default => null
        };

        if (!$instance instanceof DataMapperInterface) {
            $instanceName = match(true) {
                is_string($dataMapper) => $dataMapper,
                is_object($instance) => get_class($instance),
                default => null
            };

            $message = match (true) {
                null !== $instanceName => sprintf(
                    'Data mapper "%s" must implement "%s" interface',
                    $instanceName,
                    DataMapperInterface::class
                ),
                default => sprintf(
                    'Data mapper must implement "%s" interface',
                    DataMapperInterface::class
                )
            };

            throw new InvalidArgumentException($message);
        }

        return $instance;
    }

    public function get($dataMapper): DataMapperInterface
    {
        if (is_string($dataMapper)) {
            if (!isset($this->instances[$dataMapper])) {
                $this->instances[$dataMapper] = $this->create($dataMapper);
            }

            return $this->instances[$dataMapper];
        }

        return $this->create($dataMapper);
    }
}
