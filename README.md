# Ride: Form Library

Form library of the PHP Ride framework.

## What's In This Library

### RowFactory

The _RowFactory_ interface is used to create a _Row_ based on a simple name.
Using this factory adds flexibility to the forms since every row can be overwritten with another implementation if needed.

A default implementation is provided by the _GenericRowFactory_ class.

### Row

The _Row_ interface is used to implement a row type.
This can be a scalar type like a string, a number, ... all generic HTML form elements.
It can also be a component or a collection of components.

The following rows are provided by this library:

* button
* collection
* component
* date
* email
* file
* hidden
* image
* label
* number
* object
* option
* password
* select
* string
* text
* time
* website
* wysiwyg

### Component

The _Component_ interface is used to group a combination of rows into a single row or form.
For example, a datetime component can combine a date row and a time row together into a single row.
Creating components will add reusability to your forms.

### Widget

The _Widget_ interface is used to display a _Row_.
Certain rows can have the same logic but a different view representation.
Think about an option row which can be represented through a list of checkboxes (or radio buttons) or a select field.

### View

The _View_ interface is used to send the form to the UI.
It removes everything needed to build the form and makes sure the form is serializeable.

## Code Sample

Check this code sample to see some possibilities of this library:

```php
<?php

function createForm(Form $form) {
    // id of the form
    $form->setId('form-example');
    // action to catch submission of different forms on one page
    $form->setAction('submit-example');
    
    $form->addRow('name', 'string', array(
        'label' => 'Name',
        'description' => 'Enter your name',
        'filters' => array(
            'trim' => array(),
        ), 
        'validators' => array(
            'required' => array(),
        ),
    ));
    $form->addRow('gender', 'option', array(
        'label' => 'Gender',
        'description' => 'Select your gender',
        'default' => 'F',
        'options' => array(
            'F' => 'Female',
            'M' => 'Male',
            'O' => 'Other', 
        ),
        'validators' => array(
            'required' => array(),
        ),
    ));
    $form->addRow('extra', 'string', array(
        'label' => 'Extra',
        'description' => 'Extra row to show off some other options',
        'multiple' => true,
        'disabled' => false,
        'readonly' => false,
        'attributes' => array(
            'data-extra' => 'An extra attribute for the HTML element',
        ),
    ));
    
    return $form->build();
}

```

## Related Modules

- [ride/app-form](https://github.com/all-ride/ride-app-form)
- [ride/lib-common](https://github.com/all-ride/ride-lib-common)
- [ride/lib-image](https://github.com/all-ride/ride-lib-image)
- [ride/lib-reflection](https://github.com/all-ride/ride-lib-reflection)
- [ride/lib-system](https://github.com/all-ride/ride-lib-system)
- [ride/lib-validation](https://github.com/all-ride/ride-lib-validation)
- [ride/web-form](https://github.com/all-ride/ride-web-form)
- [ride/web-form-taxonomy](https://github.com/all-ride/ride-web-form-taxonomy)
- [ride/web-form-wysiwyg-ckeditor](https://github.com/all-ride/ride-web-form-wysiwyg-ckeditor)
- [ride/web-form-wysiwyg-tinymce](https://github.com/all-ride/ride-web-form-wysiwyg-tinymce)

## Installation

You can use [Composer](http://getcomposer.org) to install this library.

```
composer require ride/app-form
```

