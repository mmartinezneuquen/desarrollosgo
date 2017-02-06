<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "desarrollo".
 *
 * @property string $Pass
 * @property integer $EnDesarrollo
 */
class Desarrollo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'desarrollo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Pass', 'EnDesarrollo'], 'required'],
            [['EnDesarrollo'], 'integer'],
            [['Pass'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Pass' => 'Pass',
            'EnDesarrollo' => 'En Desarrollo',
        ];
    }
}
