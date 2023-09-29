<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Config;

use Magento\Framework\Config\ConverterInterface;
use MateuszMesek\Framework\Config\Converter\ItemsResolver;
use MateuszMesek\Framework\Config\Converter\ProcessorInterface;

class Converter implements ConverterInterface
{
    public function __construct(
        private readonly ItemsResolver      $itemsResolver,
        private readonly ProcessorInterface $documentProcessor,
        private readonly ProcessorInterface $indexSettingProcessor
    )
    {
    }

    public function convert($source): array
    {
        $data = [
            'document' => [],
            'index' => [
                'settings' => []
            ]
        ];

        $documents = $this->itemsResolver->resolve($source, 'document');

        foreach ($documents as $document) {
            $documentData = $this->documentProcessor->process($document);

            $data['document'][$documentData['name']] = $documentData;
        }

        $indexes = $this->itemsResolver->resolve($source, 'index');

        foreach ($indexes as $index) {
            $settings = $this->itemsResolver->resolve($index, 'setting');

            foreach ($settings as $setting) {
                $settingData = $this->indexSettingProcessor->process($setting);

                $data['index']['settings'][$settingData['path']] = $settingData['type'];
            }
        }

        return $data;
    }
}
