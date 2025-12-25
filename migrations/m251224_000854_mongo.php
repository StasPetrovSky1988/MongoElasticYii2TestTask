<?php

use yii\db\Migration;

class m251224_000854_mongo extends Migration
{

    public function up()
    {
        Yii::$app->mongodb->getCollection('invoices');

        Yii::$app->mongodb->getCollection('invoices')
            ->createIndex(
                ['date_invoice' => 1, 'subd_code' => 1, 'product_code' => 1],
                ['unique' => true, 'name' => 'uniq_invoices']
            );
    }

    public function down()
    {
        Yii::$app->mongodb->getCollection('invoices')->drop();
    }

}
