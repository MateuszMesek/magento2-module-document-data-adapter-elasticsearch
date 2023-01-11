<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Data;

interface DocumentNodeInterface
{
    /**
     * @return string
     */
    public function getDocumentName(): string;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return string|callable
     */
    public function getFieldMapper(): callable|string;

    /**
     * @return string|callable
     */
    public function getDataMapper(): callable|string;
}
