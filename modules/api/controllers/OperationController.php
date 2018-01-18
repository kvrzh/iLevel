<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Operation;
use yii\web\Response;

class OperationController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'delete' => ['delete'],
                    'edit' => ['put'],
                    'create' => ['post'],
                ],
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() // Получение всех операций ( GET запрос на /api/operation )
    {
        $data = Operation::getOperations();
        print_r(json_encode($data));
        return false;
    }

    public function actionCreate() // Создание операции ( POST запрос на /api/operation с нужными данными)
    {
        if (\Yii::$app->request->post()) {
            $values = \Yii::$app->request->post();
            $operation = new Operation();
            $values['value'] = $values['uah'];
            unset($values['uah']);
            $operation->attributes = $values;
            if ($operation->save()) {
                $data['status'] = 'OK';
                $data['id'] = $operation->id;
                echo json_encode($data);
            } else {
                print_r($operation->errors);
            }
        }
    }

    public function actionDelete($id) // Удаление операции ( DELETE запрос на /api/operation с указанием id через /)
    {
        $operation = Operation::findOne($id);
        $operation->delete();
        return false;
    }

    public function actionEdit($id) // Изменение операции ( PUT запрос на /api/operation с указанием id через "/" и нужных данных)
    {
        if ($id && \Yii::$app->request->post()) {
            $values = \Yii::$app->request->post();
            $values['value'] = $values['uah'];
            unset($values['uah']);
            $operation = Operation::findOne($id);
            $operation->attributes = $values;
            if ($operation->save()) {
                echo 'OK';
            } else {
                print_r($operation->errors);
            }
        }

    }

}
