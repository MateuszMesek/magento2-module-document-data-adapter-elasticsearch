<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\ArrayManager;

class Mapper implements DifferInterface
{
    private const MAPPINGS_PATH = 'mappings/properties';
    private const META_FIELDS = 'mappings/_meta/fields';

    public function __construct(
        private readonly ArrayManager        $arrayManager,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function check(array $new, array $current): int
    {
        $newMappings = $this->arrayManager->get(self::MAPPINGS_PATH, $new, []);
        $currentFields = $this->arrayManager->get(self::META_FIELDS, $current, []);
        $currentMappings = $this->arrayManager->get(self::MAPPINGS_PATH, $current, []);

        $newFields = array_keys($newMappings);

        $toRemoveFields = array_diff($currentFields, $newFields);

        if ($toRemoveFields) {
            return self::NEW;
        }

        $toUpdateFields = [];

        foreach ($newFields as $fieldName) {
            if (!isset($currentMappings[$fieldName])) {
                continue;
            }

            $newField = $newMappings[$fieldName];
            $currentField = $currentMappings[$fieldName];

            $serializedNewField = $this->serializer->serialize($newField);
            $serializedCurrentField = $this->serializer->serialize($currentField);

            $toUpdate = $serializedNewField !== $serializedCurrentField;

            if (!$toUpdate) {
                continue;
            }

            if ($newField['type'] !== $currentField['type']) {
                return self::NEW;
            }

            $toUpdateFields[] = $fieldName;
        }

        $toCreateFields = array_diff_assoc($newFields, $currentFields);

        if (!empty($toCreateFields) || !empty($toUpdateFields)) {
            return self::UPDATE;
        }

        return self::NONE;
    }
}
