<?php

namespace app\models;

use Yii;
use yii\base\Model;

class InvoiceElasticReportForm extends Model
{
    public $region;

    public function rules(): array
    {
        return [
            [['region'], 'string'],
        ];
    }

    public function search(array $params = []): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return $this->errors;
        }

        $query = [
            'bool' => [
                'must' => []
            ]
        ];

        if ($this->region) {
            $query['bool']['must'][] = [
                'term' => [
                    'region' => $this->region
                ]
            ];
        }

        $response = Yii::$app->elastic->search([
            'index' => 'invoices',
            'size' => 0,
            'body' => [
                'query' => $query,
                'aggs' => [
                    'regions' => [
                        'terms' => [
                            'field' => 'region',
                            'size' => 1000
                        ],
                        'aggs' => [
                            'product_codes' => [
                                'terms' => [
                                    'field' => 'product_code',
                                    'size' => 1000
                                ],
                                'aggs' => [
                                    'total_count' => [
                                        'sum' => [
                                            'field' => 'count'
                                        ]
                                    ],
                                    'product_names' => [
                                        'top_hits' => [
                                            'size' => 1,
                                            '_source' => ['product_name']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $result = [];
        $regions = $response['aggregations']['regions']['buckets'] ?? [];

        foreach ($regions as $region) {
            $regionName = $region['key'];
            $products = $region['product_codes']['buckets'] ?? [];

            foreach ($products as $product) {
                $productCode = (int) $product['key'];
                $count = $product['total_count']['value'] ?? 0;
                $productName = $product['product_names']['hits']['hits'][0]['_source']['product_name'] ?? '';

                $result[] = [
                    'region' => $regionName,
                    'product_name' => $productName,
                    'product_code' => $productCode,
                    'count' => $count
                ];
            }
        }

        return $result;
    }

    public function formName(): string
    {
        return '';
    }
}
