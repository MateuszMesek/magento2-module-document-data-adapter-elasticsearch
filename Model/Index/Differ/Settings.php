<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\Differ;

use Magento\Framework\Stdlib\ArrayManager;

class Settings implements DifferInterface
{
    private const SETTINGS_PATH = 'settings';
    private array $paths;

    public function __construct(
        private readonly ArrayManager $arrayManager,
                                      $paths = [],
        private                       $result = self::NONE
    )
    {
        $this->paths = array_keys(
            array_filter($paths)
        );
    }

    public function check(array $new, array $current): int
    {
        $newSettings = $this->arrayManager->get(self::SETTINGS_PATH, $new, []);
        $currentSettings = $this->arrayManager->get(self::SETTINGS_PATH, $current, []);

        foreach ($this->paths as $path) {
            $newValue = $this->prepareValue(
                $this->arrayManager->get($path, $newSettings)
            );
            $currentValue = $this->prepareValue(
                $this->arrayManager->get($path, $currentSettings)
            );

            if ($newValue === $currentValue) {
                continue;
            }

            return $this->result;
        }

        return self::NONE;
    }

    private function prepareValue($value): string
    {
        $this->sortValue($value);

        return json_encode($value, JSON_NUMERIC_CHECK | JSON_THROW_ON_ERROR);
    }

    private function sortValue(&$value): void
    {
        if (!is_array($value)) {
            return;
        }

        ksort($value);

        foreach ($value as &$item) {
            $this->sortValue($item);
        }
    }
}
