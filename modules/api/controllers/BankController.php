<?php

namespace app\modules\api\controllers;

class BankController extends \yii\web\Controller
{
    public function actionIndex() // Публичный метод для получения курса ( API )
    {
        $usd = $this->getMoneyInDollars();
        print_r($usd);
        return true;
    }

    private function getMoneyInDollars($money = false)
    {
        $url = "https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5";
        $usd = 0;
        $exchange = json_decode(file_get_contents($url));
        foreach ($exchange as $item) {
            if ($item->ccy === 'USD') {
                $usd = $item->buy;
            }
        }
        if ($money === false) {
            return $usd;
        }
        return $money / $usd;
    }

}
