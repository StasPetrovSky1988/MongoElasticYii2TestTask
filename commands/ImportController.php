<?php

namespace app\commands;

use app\services\ImportService;
use yii\console\Controller;

class ImportController extends Controller
{

    public function __construct($id, $module, private ImportService $importService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Импорт из csv в mongo
     * php yii import/invoices-csv
     */
    public function actionInvoicesCsv(string $path = '@core/data/БаДМ_01.10.2021-15.10.2021.csv')
    {
        $this->importService->importInvoicesCsv($path);
    }

}
