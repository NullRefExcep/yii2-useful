<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Class TranslationBehavior
 *
 * @package nullref\useful\behaviors
 *
 * @property $owner Model
 *
 *
 * ```php
 *    public function behaviors()
 *    {
 *        return [
 *            'multilanguage' => [
 *                'class' => TranslationBehavior::className(),
 *                'languages' => [1 => 'en', 2 => 'de', 3 => 'fr'],
 *                'defaultLanguage' => App::DEFAULT_LANG,
 *                'relation' = 'translations',
 *                'attributeNamePattern' => '{attr}_{lang}', //usage $model->question_fr
 *                'languageField' => 'language', //field in TranslationModel
 *                'langClassName' => TranslationModel::className(),
 *                'translationAttributes' => [
 *                    'question', 'description', //fields in TranslationModel
 *                ],
 *            ],
 *        ];
 *    }
 *
 * @author  Velychko Yaroslav
 * @author  Dmytro Karpovych <ZAYEC77@gmail.com>
 */
class TranslationBehavior extends Behavior
{

    /**
     * @var string the className of related translation class
     */
    public $langClassName;

    /**
     * @var string the abbreviation of the default language
     */
    public $defaultLanguage;

    /**
     * @var string the name of the translations relation
     */
    public $relation = 'translations';

    /**
     * @var string the language field used in the related table. Determines the language to query | save.
     */
    public $languageField = 'language';

    /**
     * @var array the list of attributes to translate. You can add validation rules on the owner.
     */
    public $translationAttributes = [];

    /**
     * @var string| \Closure
     * String pattern for translated fields
     * or closure that return field name e.g:
     *      function ($attribute, $language)
     *      {
     *          return $attribute . '_' . $language;
     *      }
     */
    public $attributeNamePattern = '{attr}_{lang}';

    /**
     * @var array the list of available languages
     */
    public $languages = [];

    /**
     * Delete related records when delete owner
     * @var bool
     */
    public $deleteTranslations = true;

    protected $_newModels = [];

    public function init()
    {
        parent::init();
        if (!in_array($this->defaultLanguage, $this->languages, true)) {
            throw new InvalidConfigException('Default language must be exist');
        }
    }

    /**
     * @inheritdoc
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
     * Delete related records when delete owner
     */
    public function afterDelete()
    {
        if ($this->deleteTranslations) {
            /** @var ActiveRecord $owner */
            foreach ($this->getModels() as $model) {
                $model->delete();
            }
        }
    }

    /**
     * @return ActiveRecord[]
     */
    protected function getModels()
    {
        $selfModels = ArrayHelper::index($this->owner->{$this->relation}, function ($value) {
            return $this->languages[$value->{$this->languageField}];
        });
        return array_merge($this->getNewModels(), $selfModels);
    }

    /**
     * @return ActiveRecord[]
     */
    protected function getNewModels()
    {
        if (count($this->_newModels) === 0) {
            foreach ($this->languages as $key => $language) {
                $this->_newModels[$language] = new $this->langClassName([$this->languageField => $key]);
            }
        }
        return $this->_newModels;
    }

    /**
     * Save related translations
     */
    public function afterSave()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        foreach ($this->getModels() as $model) {
            $owner->link($this->relation, $model);
        }
    }

    /**
     * Modify owner model validators for translation attributes
     * e.g.  `['title', 'required']` convert to `[['title_en', 'title_fr'], 'required']`
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $rules = $owner->rules();
        $validators = $owner->getValidators();
        foreach ($rules as $rule) {

            $rule_attributes = is_array($rule[0]) ? $rule[0] : [$rule[0]];
            $attributes = array_intersect($this->translationAttributes, $rule_attributes);
            if (empty($attributes)) {
                continue;
            }
            $rule_attributes = [];
            foreach ($attributes as $key => $attribute) {
                foreach ($this->languages as $language) {
                    $rule_attributes[] = $this->getAttributeName($attribute, $language);
                }
            }

            $params = array_slice($rule, 2);
            $validators->append(Validator::createValidator($rule[1], $owner, $rule_attributes, $params));
        }
    }

    /**
     * @param $attribute
     * @param $language
     * @return mixed|string
     */
    private function getAttributeName($attribute, $language)
    {
        if ($this->attributeNamePattern instanceof \Closure) {
            return call_user_func_array($this->attributeNamePattern, [$attribute, $language]);
        }
        return strtr($this->attributeNamePattern, [
            '{attr}' => $attribute,
            '{lang}' => $language,
        ]);
    }

    /**
     * Check translation attributes
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if ($this->hasOwnProperty($name)) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    /**
     * Check translation attributes
     * @param $name
     * @return bool
     */
    public function hasOwnProperty($name)
    {
        foreach ($this->translationAttributes as $attribute) {
            if ($name === $attribute) {
                return true;
            }
            foreach ($this->languages as $language) {
                if ($this->getAttributeName($attribute, $language) === $name) {
                    return true;
                }
            }
        }
    }

    /**
     * Check translation attributes
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if ($this->hasOwnProperty($name)) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $list = $this->getModels();

        foreach ($this->translationAttributes as $attribute) {
            if ($name === $attribute) {
                return $list[$this->defaultLanguage]->{$attribute};
            }
            foreach ($list as $language) {
                $langKey = $this->languages[$language->{$this->languageField}];
                if ($this->getAttributeName($attribute, $langKey) === $name) {
                    return $list[$langKey]->{$attribute};
                }
            }
        }
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $list = $this->getModels();

        foreach ($this->translationAttributes as $attribute) {
            if ($name === $attribute) {
                return $list[$this->defaultLanguage]->{$attribute} = $value;
            }
            foreach ($list as $language) {
                $langKey = $this->languages[$language->{$this->languageField}];
                if ($this->getAttributeName($attribute, $langKey) === $name) {
                    return $list[$langKey]->{$attribute} = $value;
                }
            }
        }
        parent::__set($name, $value);
    }

}
