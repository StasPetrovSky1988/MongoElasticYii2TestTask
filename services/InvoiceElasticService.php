<?php

namespace app\services;

use app\models\InvoiceMongo;
use yii\mongodb\ActiveQuery;

class InvoiceElasticService
{

    /**
     * Индексация invoices
     */
    public function indexing(ActiveQuery $query): void
    {
        $bulkHandler = new InvoiceElasticBulkHandler(100);

        foreach ($query->each() as $invoice) {
            $bulkHandler->add($invoice);
        }

        $bulkHandler->flush();

        echo PHP_EOL . 'Индексация завершена. ' . PHP_EOL;
    }

    /**
     * Сериализация InvoiceMongo
     */
    public static function transform(InvoiceMongo $invoiceMongo): array
    {
        $dt = $invoiceMongo->date_invoice->toDateTime();

        return [
            'ref_id' => (string) $invoiceMongo->_id,
            'region' => $invoiceMongo->region,
            'count' => $invoiceMongo->count,
            'product_name' => $invoiceMongo->product_name,
            'product_code' => $invoiceMongo->product_code,
            'date_invoice' => $dt->format('Y/m/d'),
        ];
    }

    /**
     * Unique key для Invoice
     */
    public static function get_ID(InvoiceMongo $invoiceMongo): string
    {
        return $invoiceMongo->date_invoice . '.' . $invoiceMongo->product_code . '.' . $invoiceMongo->subd_code;
    }

}
