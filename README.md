Yii2-useful
=====================
[![Latest Stable Version](https://poser.pugx.org/nullref/yii2-useful/v/stable)](https://packagist.org/packages/nullref/yii2-useful) [![Total Downloads](https://poser.pugx.org/nullref/yii2-useful/downloads)](https://packagist.org/packages/nullref/yii2-useful) [![Latest Unstable Version](https://poser.pugx.org/nullref/yii2-useful/v/unstable)](https://packagist.org/packages/nullref/yii2-useful) [![License](https://poser.pugx.org/nullref/yii2-useful/license)](https://packagist.org/packages/nullref/yii2-useful)


Collection helpful classes for Yii2

## Installation
```bash
composer require nullref/yii2-useful --prefer-dist
```

## Structure

### Actions
- #### EditAction
    
    Action for AJAX model update

- #### MultipleUpdateAction
    
    Allows to update multiple models

### Behaviors
- #### BinaryBehavior
    
    Allows encode and decode model fields as integer number.

- #### DateBehavior
    Allows encode and decode model fields as Unix timestamp.

- #### JsonBehavior

    Allows encode and decode model fields as JSON.

- #### RelatedBehavior

    Allows to load related data for model.

- #### SerializeBehavior

    Allows encode and decode model fields as serialize php string.

- #### TranslationBehavior

    Allow to implement multilingual features.

### Traits
- #### GetDefinition

    Allows to get defined in DI container class or default.

- #### HasPassword
    
    Allows to set and validate password for model.

- #### Mappable
    
    Allows to get map from AR records.
    
### Filters
- #### RedirectFilter
    Redirect after action by url param

### Helpers
- #### Memoize
    
    Class for calling memoized function or method



## Old version
(Will be remove from version 1.0.0)

### JSONBehavior
Allows save in text field customs data structure.
### ArrayBehavior
Allows save in text field array structure.
### BitBehavior
Allows save in integer field bit array.
### DropDownTrait
Allows get lists from ActiveRecord.
### PasswordTrait
Easy work with password.
### EditAction
Action for AJAX record update.
### MultipleUpdateAction
Action multiple update by list of IDs.
### TranslationBehavior
Allows implement translations for models

