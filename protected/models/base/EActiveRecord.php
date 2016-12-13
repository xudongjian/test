<?php

abstract class EActiveRecord extends CActiveRecord {

    const DB_FORMAT_DATETIME = 'Y-m-d H:i:s';
    const DB_FORMAT_DATE = 'Y-m-d';

    /**
     * Prepares date_created, date_updated.
      id attributes before performing validation.
     */
    protected function beforeValidate() {
        // date_created, date_updated.
        $now = new CDbExpression('NOW()');
        if ($this->isNewRecord) {
            // a new record, so set the date_created.
            $this->setDateCreated($now);
        }
        $this->setDateUpdated($now);
        
        $attributes = $this->trimAttributes();
        if(is_array($attributes) && count($attributes)>0){
            foreach($attributes as $attribute){
                $this->{$attribute}=trim($this->{$attribute});
            }
        }
        return parent::beforeValidate();
    }

    // Change datetime format after record is queried from db.
    protected function afterFind() {
        parent::afterFind();
    }
    
    protected function trimAttributes(){
        return array();
    }


    public function getSafeAttributes() {
        $safeAttributeNames = $this->getSafeAttributeNames();
        return $this->getAttributes($safeAttributeNames);
    }

    /**
     * 
     * @return array the first error of each attribute.
     */
    public function getFirstErrors() {
        $ret = array();
        $errorList = $this->getErrors();
        if (emptyArray($errorList) === false) {
            foreach ($errorList as $attribute => $errors) {
                if (emptyArray($errors) === false) {
                    $error = array_shift($errors);
                    $ret[$attribute] = $error;
                }
            }
        }
        return $ret;
    }

    protected function dateToDBFormat($dateStr) {
        $date = new DateTime($dateStr);
        if ($date === false)
            return null;
        else
            return $date->format(self::DB_FORMAT_DATE);
    }

    protected function datetimeToDBFormat($dateStr) {
        $date = new DateTime($dateStr);
        if ($date === false) {
            return null;
        } else
            return $date->format(self::DB_FORMAT_DATETIME);
    }

    protected function getDateAttribute($dateStr, $format = null) {
        if (empty($dateStr)) {
            return null;
        }
        if (is_null($format)) {
            $format = 'Y年m月d日';
        }
        $date = new DateTime($dateStr);
        return $date->format($format);
    }

    protected function setDateAttribute($dateStr) {
        if (empty($dateStr)) {
            return null;
        } else {
            return $this->dateToDBFormat($dateStr);
        }
    }

    protected function getDatetimeAttribute($datetimeStr, $format = null) {
        if (empty($datetimeStr)) {
            return null;
        }
        if (is_null($format)) {
            $format = 'Y年m月d日 H:i';
        }
        $datetime = new DateTime($datetimeStr);
        return $datetime->format($format);
    }

    protected function setDatetimeAttribute($datetimeStr) {
        if (empty($datetimeStr)) {
            return null;
        } else {
            return $this->datetimeToDBFormat($datetimeStr);
        }
    }

    protected function getTextAttribute($value, $ntext = true) {
        if ($ntext) {
            return Yii::app()->format->formatNtext($value);
        } else {
            return $value;
        }
    }

    protected function getNullAttribute($value, $nullStr = "") {
        if (is_null($value)) {
            return $nullStr;
        } else {
            return $value;
        }
    }

    protected function getBooleanAttribute($value) {
        return ($value == 1 || $value === true);
    }

    public function setEmptyAttributeToNull($attribute) {
        if (empty($this->{$attribute}) || $this->{$attribute} == '')
            $this->{$attribute} = null;
    }

    /*     * ****** Query Methods ******* */
    /*
     * Override parent implementation.
     */

    public function findByPk($pk, $condition = '', $params = array()) {
        if ($pk === null) {
            return null;
        } else {
            return parent::findByPk($pk, $condition, $params);
        }
    }

    /**
     * order by ids => 'order'=>FIELD(id, 2,3,1).
     * @param type $ids
     * @param type $with
     * @return type 
     */
    public function getAllByIds($ids, $with = null) {
        // return $this->getAllByInCondition('id', $ids, $with);
        if (is_array($ids) === false) {
            $ids = array($ids);
        }
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->addInCondition('t.id', $ids);
        $criteria->order = "FIELD(t.id," . arrayToCsv($ids) . ")";
        if (is_array($with)) {
            $criteria->with = $with;
        }

        return $this->findAll($criteria);
    }

    /*
     * @param $field string the column to be compared.
     * @param $values array values to be passed into "IN" condition.
     * Sample Query: select * from table where $field IN $values.
     */

    public function getAllByInCondition($field, $values, $with = null) {
        if (is_array($values) === false) {
            $values = array($values);
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->addInCondition($field, $values);
        if (is_array($with)) {
            $criteria->with = $with;
        }

        return($this->findAll($criteria));
    }

    public function getAll($with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');

        if (is_array($with)) {
            $criteria->with = $with;
        }
        if (isset($options['order'])) {
            $criteria->order = $options['order'];
        }
        if (isset($options['offset'])) {
            $criteria->offset = $options['offset'];
            $criteria->limit = 10;    // limit must be defined if offset is applied.
        }
        if (isset($options['limit'])) {
            $criteria->limit = $options['limit'];
        }

        return ($this->findAll($criteria));
    }

    /*
      public function getAll($with = null, $sorts = array(), $limit = 0, $offset = 0) {
      $criteria = new CDbCriteria();
      $criteria->addCondition('t.date_deleted is NULL');

      if (is_array($with)) {
      $criteria->with = $with;
      }

      if (empty($sorts) === false) {
      $order = implode($sorts);
      $criteria->order = $order;
      }
      if ($limit > 0) {
      $criteria->limit = $limit;
      }
      if ($offset > 0) {
      $criteria->offset = $offset;
      }

      return ($this->findAll($criteria));
      }
     * 
     */

    public function getById($id, $with = null) {
        if (is_null($id))
            return null;
        else if (is_array($with)) {
            return $this->with($with)->findByAttributes(array('id' => $id, 'date_deleted' => null));
        } else {
            return $this->findByAttributes(array('id' => $id, 'date_deleted' => null));
        }
    }

    /**
     *  Query model with relations, 'date_deleted' is null by default if not specified.
     * @param array $attrs
     * @param type $with array of model's relations.
     * @return type 
     */
    public function getByAttributes(array $attrs, $with = null) {
        if (isset($attrs['date_deleted']) === false)
            $attrs['date_deleted'] = null;
        if (is_array($with))
            return $this->with($with)->findByAttributes($attrs);
        else
            return $this->findByAttributes($attrs);
    }

    /**
     *  Query model with relations, 'date_deleted' is null by default if not specified.
     * @param array $attrs
     * @param type $with model's relations.
     * @return type 
     */
    public function getAllByAttributes(array $attrs, $with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        foreach ($attrs as $attr => $value) {
            $criteria->compare($attr, $value);
        }
        if (isset($with) && is_array($with)) {
            $criteria->with = $with;
        }
        if (isset($options['order'])) {
            $criteria->order = $options['order'];
        }
        if (isset($options['offset'])) {
            $criteria->offset = $options['offset'];
            $criteria->limit = 10;    // limit must be defined if offset is applied.
        }
        if (isset($options['limit'])) {
            $criteria->limit = $options['limit'];
        }

        return $this->findAll($criteria);
    }

    /*
     * Mark record as deleted (date_deleted is not null).
     */

    public function delete($absolute = true) {
        if ($absolute) {
            return parent::delete();
        } else {
            if (!$this->getIsNewRecord()) {
                Yii::trace(get_class($this) . '.delete()', 'system.db.ar.CActiveRecord');
                $now = new CDbExpression('NOW()');
                return $this->updateByPk($this->id, array('date_deleted' => $now));
            } else
                throw new CDbException(Yii::t('yii', 'The active record cannot be deleted because it is new.'));
        }
    }

    /**
     *
     * @param array $fields - the fields to be updated.
     * @param array $attrs - the attributes for comparison.
     */
    public function updateAllByAttributes(array $fields, array $attrs) {
        $criteria = new CDbCriteria();
        if (is_array($attrs) && count($attrs) > 0) {
            foreach ($attrs as $attr => $value) {
                $criteria->compare($attr, $value);
                //$criteria->addCondition($attr . '=' . $value);
            }
        }
        return $this->updateAll($fields, $criteria);
    }

    /*     * ****** Accessors ******* */

    public function getId() {
        return ($this->id);
    }

    public function setId($v) {
        $this->id = $v;
    }

    public function getDateCreated($format = 'Y年m月d日 H:i:s') {
        $date = new DateTime($this->date_created);
        return $date->format($format);
    }

    private function setDateCreated($v) {
        $this->date_created = $v;
    }

    public function getDateUpdated($format = 'Y年m月d日 h:i:s') {
        $date = new DateTime($this->date_updated);
        return $date->format($format);
    }

    private function setDateUpdated($v) {
        $this->date_updated = $v;
    }

    public function getDateDeleted($format = 'Y年m月d日 h:i:s') {
        $date = new DateTime($this->date_deleted);
        return $date->format($format);
    }

    private function setDateDeleted($v) {
        $this->date_deleted = $v;
    }

}
