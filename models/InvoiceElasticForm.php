<?php

namespace app\models;

use Yii;
use yii\base\Model;

class InvoiceElasticForm extends Model
{
    public $date_invoice_from;
    public $date_invoice_to;
    public $product_code;
    public $product_name;

    public function rules(): array
    {
        return [
            [['product_name', 'product_code'], 'string'],
            [['date_invoice_from', 'date_invoice_to'], 'date', 'format' => 'php:Y-m-d'],
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

        if ($this->product_name) {
            $query['bool']['must'][] = [
                'match' => [
                    'product_name' => [
                        'query' => mb_strtolower($this->product_name),
                        'fuzziness' => 'AUTO',
                    ]
                ]
            ];
        }

        if ($this->product_code) {
            $query['bool']['must'][] = [
                'term' => [
                    'product_code' => $this->product_code
                ]
            ];
        }

        if ($this->date_invoice_from || $this->date_invoice_to) {
            $rangeQuery = [];

            if ($this->date_invoice_from) {
                $rangeQuery['gte'] = str_replace('-', '/', $this->date_invoice_from);
            }

            if ($this->date_invoice_to) {
                $rangeQuery['lte'] = str_replace('-', '/', $this->date_invoice_to);
            }

            $query['bool']['must'][] = [
                'range' => [
                    'date_invoice' => $rangeQuery
                ]
            ];
        }

        $response = Yii::$app->elastic->search([
            'index' => 'invoices',
            'size' => 1000,
            'body' => [
                'query' => $query,
            ]
        ]);

        return array_map(fn($hit) => $hit['_source'] ?? [], $response['hits']['hits'] ?? []);
    }
    
    public function formName(): string
    {
        return '';
    }
}