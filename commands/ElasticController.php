<?php

namespace app\commands;

use app\models\InvoiceMongo;
use app\services\InvoiceElasticService;
use yii\console\Controller;

class ElasticController extends Controller
{

    public function __construct($id, $module, private InvoiceElasticService $invoiceElasticService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Индексация данных из МонгоДБ в Эластик
     * php yii elastic/indexing
     */
    public function actionIndexing()
    {
        $this->invoiceElasticService->indexing(InvoiceMongo::find());
    }

}
