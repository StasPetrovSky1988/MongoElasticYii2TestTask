<?php

namespace app\controllers;

use app\models\InvoiceElasticForm;
use app\models\InvoiceElasticReportForm;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    public function actionIndex()
    {
        return ['info' => 'Hello by Stas Petrov! This project is test task for "Омега-консалтинг, ООО"'];
    }

    /**
     * Пример запроса
     * http://localhost:8080/invoice?product_code=4785003&product_name=стрептоцид&date_invoice_to=2021-10-01&date_invoice_from=2021-09-01
     */
    public function actionInvoice()
    {
        $form = new InvoiceElasticForm();

        return $form->search(Yii::$app->request->queryParams);
    }

    /**
     * Пример запроса
     * http://localhost:8080/report?region=КИЕВСКАЯ
     */
    public function actionReport()
    {
        $form = new InvoiceElasticReportForm();

        return $form->search(Yii::$app->request->queryParams);
    }


}
