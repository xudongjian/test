<?php

class User extends CActiveRecord {

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('age', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 50),
            array('id, name, age', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '名字',
            'age' => '年龄',
        );
    }

    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
