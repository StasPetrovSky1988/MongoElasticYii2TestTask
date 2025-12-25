<?php

namespace app\models;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * @property ObjectId $_id
 * @property string $region
 * @property int $count
 * @property string $product_name
 * @property int $product_code
 * @property UTCDateTime $date_invoice
 * @property UTCDateTime $date_end_license
 * @property string $subd_code
 */
class InvoiceMongo extends \yii\mongodb\ActiveRecord
{
    public static function collectionName(): string
    {
        return 'invoices';
    }

    public function rules(): array
    {
        return [
            [['date_invoice', 'subd_code', 'product_code'], 'required'],
            [['date_invoice', 'date_end_license', '_id'], 'safe'],
            [['client_code', 'okpo_client', 'product_code', 'barcode', 'mor_code', 'count'], 'integer'],
            [['firm', 'region', 'city', 'shipping_address', 'legal_address', 'client', 'subd_code', 'license', 'product_name', 'ei', 'producer', 'supplier', 'store'], 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            '_id',
            'firm',
            'region',
            'city',
            'date_invoice',
            'shipping_address',
            'legal_address',
            'client',
            'client_code',
            'subd_code',
            'okpo_client',
            'license',
            'date_end_license',
            'product_code',
            'barcode',
            'product_name',
            'mor_code',
            'ei',
            'producer',
            'supplier',
            'count',
            'store',
        ];
    }

}
