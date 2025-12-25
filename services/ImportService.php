<?php

namespace app\services;

use MongoDB\BSON\UTCDateTime;
use Yii;
use yii\console\Exception;

class ImportService
{

    /**
     * Импорт данных из файла
     */
    public function importInvoicesCsv(string $path): void
    {
        $path = Yii::getAlias($path);

        if (!is_file($path)) {
            throw new Exception("Файл не найден. " . $path);
        }

        if (!$handle = fopen($path, 'r')) {
            throw new Exception("Не удавлось открыть файл. " . $path);
        }

        $mapFields = [
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

        // Пропустим заголовки
        fgetcsv($handle, 0);

        $i = 1;
        $imported = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $i++;

            // Удалим лишние символы
            $row = array_map(function ($value) {
                return trim($value, ' "');
            }, $row);

            // Мапим строку
            if (!$row = array_combine($mapFields, $row)) {
                continue;
            }

            // Переопределим типы
            $row['date_invoice'] = new UTCDateTime(strtotime($row['date_invoice']) * 1000);
            $row['date_end_license'] = new UTCDateTime(strtotime($row['date_end_license']) * 1000);
            $row['product_code'] = (int) $row['product_code'];
            $row['client_code'] = (int) $row['client_code'];
            $row['count'] = (int) $row['count'];
            $row['mor_code'] = (int) $row['mor_code'];
            $row['okpo_client'] = (int) $row['okpo_client'];

            $collection = Yii::$app->mongodb->getCollection('invoices');

            // Если данных много, нужно переделать через bulk вставку
            $imported += $collection->update(
                ['date_invoice' => $row['date_invoice'], 'subd_code' => $row['subd_code'], 'product_code' => $row['product_code']],
                ['$setOnInsert' => $row],
                ['upsert' => true]
            );

            if ($i % 100 === 0) {
                echo '.';
            }
        }

        echo PHP_EOL . 'Импорт файла ' . $path . ' выполнен. Импортированно ' . $imported . ' строк' . PHP_EOL;
    }
}
