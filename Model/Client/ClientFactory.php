<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataAdapterElasticsearch\Model\Client;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilderFactory;
use Enqueue\Dsn\Dsn;

class ClientFactory
{
    public function __construct(
        private readonly ClientBuilderFactory $clientBuilderFactory
    )
    {
    }

    public function create(string $dsn): Client
    {
        $dsns = Dsn::parse($dsn);

        $clientBuilder = $this->clientBuilderFactory->create();

        if ($hosts = $this->getHosts($dsns)) {
            $clientBuilder->setHosts($hosts);
        }

        return $clientBuilder->build();
    }

    private function getHosts(array $dsns): ?array
    {
        $hosts = array_map(
            static function (Dsn $dsn) {
                return sprintf(
                    '%s://%s:%d',
                    $dsn->getScheme(),
                    $dsn->getHost(),
                    $dsn->getPort()
                );
            },
            $dsns
        );

        if (empty($hosts)) {
            return null;
        }

        return $hosts;
    }
}
