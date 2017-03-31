<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class RelatedBehavior
 *
 * Allows to load related data for model
 *
 * ```php
 *  // config in model
 *
 *  public function behaviors()
 *  {
 *      return [
 *          'related' => [
 *              'class' => RelatedBehavior::className(),
 *              'filedSuffix' => '_list', // default 'List'
 *              'fields' => [
 *                  'relation' => RelatedModel::className(),
 *              ]
 *          ],
 *      ];
 *  }
 *
 *  // usage in action
 *
 *  public function actionUpdate($id)
 *  {
 *      $model = $this->findModel($id);
 *
 *      $default = Yii::$app->request->isPost ? ['RelatedModel' => []] : [];
 *      if ($model->loadWithRelations(array_merge($default, Yii::$app->request->post())) && $model->save()) {
 *          return $this->redirect(['view', 'id' => $model->id]);
 *      } else {
 *          return $this->render('update', [
 *              'model' => $model,
 *          ]);
 *      }
 *  }
 *
 * @package nullref\useful\behaviors
 *
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class RelatedBehavior extends Behavior
{
    /** @var array */
    public $fields = [];

    /** @var string */
    public $filedSuffix = 'List';

    /** @var string */
    public $newKeyPrefix = 'new_';

    /** @var null| string | callable */
    public $indexBy = null;

    /** @var bool */
    public $createDefault = false;

    /** @var ActiveRecord[][] */
    protected $_newValues = [];
    /** @var ActiveRecord[][] */
    protected $_editedValues = [];
    /** @var ActiveRecord[][] */
    protected $_removedValues = [];

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function loadWithRelations($data, $formName = null)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach ($this->fields as $name => $class) {
            $reflection = new \ReflectionClass($class);
            $relatedFormName = $reflection->getShortName();
            if (isset($data[$relatedFormName])) {
                /** @var ActiveRecord[] $models */
                $models = [];
                foreach ($data[$relatedFormName] as $key => $item) {
                    $models[$key] = new $class();
                    $models[$key]->setAttributes($item);
                }
                $owner->{$name . $this->filedSuffix} = $models;
            }
        }

        return $owner->load($data, $formName);
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        $originalName = str_replace($this->filedSuffix, '', $name);
        if (isset($this->fields[$originalName])) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        $originalName = str_replace($this->filedSuffix, '', $name);
        if (isset($this->fields[$originalName])) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        foreach ($this->fields as $name => $class) {
            $this->_newValues[$name . $this->filedSuffix] = [];
            $this->_removedValues[$name . $this->filedSuffix] = [];
            $this->_editedValues[$name . $this->filedSuffix] = [];
            if (!class_exists($class)) {
                throw new InvalidConfigException("Class $class doesn't exist");
            }
        }

        parent::init();
    }

    /**
     * @param string $name
     * @return mixed|\yii\db\ActiveRecord[]
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        $originalName = str_replace($this->filedSuffix, '', $name);
        if (isset($this->fields[$originalName])) {
            /** @var ActiveRecord $owner */
            $owner = $this->owner;
            if ($owner->isNewRecord) {
                $result = $this->_newValues[$name];
            } else {
                $result = $owner->getRelation($originalName)->indexBy($this->indexBy)->all();
            }
            if (empty($result) && $this->createDefault) {
                return [new $this->fields[$originalName]()];
            }

            return $result;
        }
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed $models
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $models)
    {
        $originalName = str_replace($this->filedSuffix, '', $name);
        if (isset($this->fields[$originalName])) {
            /** @var ActiveRecord $owner */
            $owner = $this->owner;
            if ($owner->isNewRecord) {
                $this->_newValues[$name] = $models;
            } else {
                $storedValues = $owner->getRelation($originalName)->indexBy($this->indexBy)->all();
                foreach ($models as $key => $model) {
                    /** @var $model ActiveRecord */
                    if ((substr($key, 0, strlen($this->newKeyPrefix)) == $this->newKeyPrefix) || ($this->indexBy && !array_key_exists($key, $storedValues))) {
                        $this->_newValues[$name][] = $model;
                    } else {
                        $this->_editedValues[$name][$key] = $storedValues[$key];
                        $this->_editedValues[$name][$key]->setAttributes($model->getAttributes());
                    }
                }
                $this->_removedValues[$name] = array_diff_key($storedValues, $this->_editedValues[$name]);
                $this->_editedValues[$name] = array_diff_key($this->_editedValues[$name], $this->_removedValues[$name]);
            }
        } else {
            parent::__set($name, $models);
        }
    }

    /**
     * @throws \Exception
     */
    public function afterSave()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach ($this->fields as $name => $class) {
            $originalName = $name . $this->filedSuffix;
            foreach ($this->_newValues[$originalName] as $model) {
                $owner->link($name, $model);
            }
            foreach ($this->_editedValues[$originalName] as $model) {
                $owner->link($name, $model);
            }
            foreach ($this->_removedValues[$originalName] as $model) {
                $model->delete();
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function afterDelete()
    {
        foreach ($this->fields as $name => $class) {
            $originalName = $name . $this->filedSuffix;
            foreach ($this->{$originalName} as $model) {
                $model->delete();
            }
        }
    }
}
