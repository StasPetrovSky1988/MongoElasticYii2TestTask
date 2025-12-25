<?php

namespace app\services;

use app\models\InvoiceMongo;
use Yii;
use yii\console\Exception;
use yii\helpers\Json;

class InvoiceElasticBulkHandler
{
    private array $items = [];

    public function __construct(private int $butchSize = 100) { }

    public function add(InvoiceMongo $invoiceMongo): void
    {
        $this->items[] = $invoiceMongo;

        if (count($this->items) >= $this->butchSize) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        if (empty($this->items)) {
            return;
        }

        $bulkParams = ['body' => []];

        foreach ($this->items as $item) {
            $row = InvoiceElasticService::transform($item);

            $bulkParams['body'][] = [
                'index' => [
                    '_index' => 'invoices',
                    '_id' => InvoiceElasticService::get_ID($item)
                ]
            ];

            $bulkParams['body'][] = $row;
        }

        $this->items = [];

        $response = Yii::$app->elastic->bulk($bulkParams);

        if ($response['errors']) {
            Yii::error(['Ошибка при индексации данных в Elasticsearch' => Json::encode($response)], 'elastic');
            throw new Exception('Ошибка при индексации данных в Elasticsearch');
        }

        echo "+";
    }

}
