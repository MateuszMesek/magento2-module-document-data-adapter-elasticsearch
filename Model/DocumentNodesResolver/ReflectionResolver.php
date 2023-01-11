<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\DocumentNodesResolver;

use Generator;
use Magento\Framework\Reflection\FieldNamer;
use Magento\Framework\Reflection\MethodsMap;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data\DocumentNodeFactory;
use MateuszMesek\DocumentDataAdapterElasticsearch\Model\Index\FieldMapper\Auto;

class ReflectionResolver implements DocumentNodesResolverInterface
{
    private array $ignoreKeys;

    public function __construct(
        private readonly string              $documentName,
        private readonly DocumentNodeFactory $documentNodeFactory,
        private readonly MethodsMap          $methodsMap,
        private readonly FieldNamer          $fieldNamer,
        private readonly string              $type,
        array                                $ignoreKeys = [],
        private readonly array               $fieldMapperByKey = [],
        private readonly array               $fieldMapperByReturnType = [],
    )
    {
        $this->ignoreKeys = array_keys(
            array_filter(
                $ignoreKeys
            )
        );
    }

    public function resolve(): Generator
    {
        $methods = $this->methodsMap->getMethodsMap($this->type);

        foreach (array_keys($methods) as $methodName) {
            if (!$this->methodsMap->isMethodValidForDataField($this->type, $methodName)) {
                continue;
            }

            $key = $this->fieldNamer->getFieldNameForMethodName($methodName);

            if (in_array($key, $this->ignoreKeys, true)) {
                continue;
            }

            yield $this->documentNodeFactory->create([
                'documentName' => $this->documentName,
                'path' => $key,
                'fieldMapper' => $this->detectFieldMapper($methodName)
            ]);
        }
    }

    private function detectFieldMapper(string $methodName): string
    {
        $key = $this->fieldNamer->getFieldNameForMethodName($methodName);

        if (isset($this->fieldMapperByKey[$key])) {
            return $this->fieldMapperByKey[$key];
        }

        $returnType = $this->methodsMap->getMethodReturnType($this->type, $methodName);

        if (str_ends_with($returnType, '[]')) {
            $returnType = substr($returnType, 0, -2);
        }

        return $this->fieldMapperByReturnType[$returnType] ?? Auto::class;
    }
}
