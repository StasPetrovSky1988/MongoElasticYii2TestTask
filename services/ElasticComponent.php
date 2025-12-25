<?php

namespace app\services;

use OpenSearch\Client;
use OpenSearch\SymfonyClientFactory;
use yii\base\Component;

/**
 * Прокидываем Client эластика в компоненты Yii2
 */
class ElasticComponent extends Component
{

    public string $base_uri = '';

    private ?Client $client = null;

    public function init(): void
    {
        $this->client = (new SymfonyClientFactory())->create([
            'base_uri' => $this->base_uri,
        ]);
    }

    public function __call($name, $params)
    {
        return $this->client->$name(...$params);
    }

}
