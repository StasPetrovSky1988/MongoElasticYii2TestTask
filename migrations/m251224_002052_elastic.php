<?php

use yii\db\Migration;

class m251224_002052_elastic extends Migration
{
    public function up()
    {
        Yii::$app->elastic->indices()->create([
            'index' => 'invoices',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'ru_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_stop',
                                    'russian_stemmer',
                                ],
                            ],
                        ],
                        'filter' => [
                            'russian_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_',
                            ],
                            'russian_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian',
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'dynamic' => 'strict',
                    'properties' => [
                        'ref_id' => ['type' => 'keyword'],
                        'region' => ['type' => 'keyword'],
                        'product_name' => [
                            'type' => 'text',
                            'analyzer' => 'ru_analyzer',
                            'search_analyzer' => 'ru_analyzer',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                ],
                            ],
                        ],
                        'count' => ['type' => 'integer'],
                        'product_code' => ['type' => 'keyword'],
                        'date_invoice' => [
                            'type' => 'date',
                            'format' => 'yyyy/MM/dd',
                        ],
                    ],
                ],
            ],
        ]);

    }

    public function down()
    {
        Yii::$app->elastic->indices()->delete([
            'index' => 'invoices',
        ]);
    }

}
