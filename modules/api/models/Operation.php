<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "operation".
 *
 * @property int $id
 * @property string $title
 * @property string $date
 * @property string $type
 * @property string $value
 */
class Operation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['date'], 'safe'],
            [['type'], 'string'],
            [['value'], 'number'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }

    public static function getOperations($post = null)
    {
        $operations = Operation::find()->orderBy('date')->asArray()->all();
        if ($post) {
            $from = strlen($post['date_from']) > 0 ? $post['date_from'] : $operations[0]['date'];
            $to = strlen($post['date_to']) > 0 ? $post['date_to'] : $operations[count($operations) - 1]['date'];
            $operations = Operation::find()->where(['between', 'date', $from, $to])->orderBy('date')->asArray()->all();
        }
        $data['first'] = $post['date_from'] ? $post['date_from'] : $operations[0]['date'];
        $data['last'] = $post['date_to'] ? $post['date_to'] : $operations[count($operations) - 1]['date'];
        $data['sum'] = static::sum($operations);
        $data['operations'] = $operations;
        return $data;
    }

    private static function sum($operations)
    {
        $sum = 0;
        foreach ($operations as $operation) {
            if ($operation['type'] === 'loss') {
                $sum -= $operation['value'];
            } else {
                $sum += $operation['value'];
            }
        }
        return $sum;
    }
}
