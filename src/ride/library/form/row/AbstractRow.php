<?php

namespace ride\library\form\row;

use ride\library\form\widget\GenericWidget;
use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;
use ride\library\validation\filter\Filter;
use ride\library\validation\validator\Validator;
use ride\library\validation\validator\RequiredValidator;

/**
 * Row for a form: container of the label and the control(s)
 */
abstract class AbstractRow implements Row {

    /**
     * Name of the label option
     * @var string
     */
    const OPTION_LABEL = 'label';

    /**
     * Name of the description option
     * @var string
     */
    const OPTION_DESCRIPTION = 'description';

    /**
     * Name of the default value option
     * @var string
     */
    const OPTION_DEFAULT = 'default';

    /**
     * Name of the disabled option
     * @var string
     */
    const OPTION_DISABLED = 'disabled';

    /**
     * Name of the readonly option
     * @var string
     */
    const OPTION_READ_ONLY = 'readonly';

    /**
     * Option for the field attributes
     * @var string
     */
    const OPTION_ATTRIBUTES = 'attributes';

    /**
     * Name of the filters option
     * @var string
     */
    const OPTION_FILTERS = 'filters';

    /**
     * Name of the validators option
     * @var string
     */
    const OPTION_VALIDATORS = 'validators';

    /**
     * Option to use an array value
     * @var string
     */
    const OPTION_MULTIPLE = 'multiple';

    /**
     * Name of the type
     * @var string
     */
    protected $type;

    /**
     * Name of the row
     * @var string
     */
    protected $name;

    /**
     * Options for the row
     * @var array
     */
    protected $options;

    /**
     * Widget for this row
     * @var ride\library\form\widget\Widget
     */
    protected $widget;

    /**
     * Flag to see if this row has been rendered
     * @var boolean
     */
    protected $isRendered;

    /**
     * Filters for this row
     * @var array
     */
    protected $filters;

    /**
     * Validators for this row
     * @var array
     */
    protected $validators;

    /**
     * Constructs a new form row
     * @param string $name Name of the row
     * @param array $options Extra options for the row or type implementation
     * @return null
     */
    public function __construct($name, array $options) {
        $this->type = static::TYPE;
        $this->name = $name;
        $this->options = $options;
        $this->data = null;
        $this->widget = null;
        $this->isRendered = false;
        $this->filters = array();
        $this->validators = array();
    }

    /**
     * Gets the type of this row
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the name of this row
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the property name for a row
     * @param string $prefix Prefix for the row name
     * @return string
     */
    protected function getPropertyName($prefix) {
        if ($prefix === '[') {
            return $this->name;
        }

        $property = $prefix . $this->name;
        if (substr($prefix, -1) == '[') {
            $property .= ']';
        }

        return $property;
    }

    /**
     * Sets an option
     * @param string $option Name of the option
     * @param mixed $value
     * @return null
     */
    public function setOption($option, $value) {
        $this->options[$option] = $value;
    }

    /**
     * Gets the options
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Gets an option from the row
     * @param string $option Name of the option
     * @param mixed $default Default value when the option is not set
     * @return mixed
     */
    public function getOption($option, $default = null) {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return $default;
    }

    /**
     * Gets the label for this row
     * @return string
     */
    public function getLabel() {
        return $this->getOption(self::OPTION_LABEL, $this->name);
    }

    /**
     * Gets the description for this row
     * @return string|null
     */
    public function getDescription() {
        return $this->getOption(self::OPTION_DESCRIPTION);
    }

    /**
     * Gets whether the field is disabled
     * @return boolean
     */
    public function isDisabled() {
        return $this->getOption(self::OPTION_DISABLED, false);
    }

    /**
     * Gets whether the field is readonly
     * @return boolean
     */
    public function isReadOnly() {
        return $this->getOption(self::OPTION_READ_ONLY, false);
    }

    /**
     * Gets the default value for the row
     * @return mixed
     */
    public function getDefault() {
        return $this->getOption(self::OPTION_DEFAULT);
    }

    /**
     * Gets the widget for this row
     * @return ride\library\form\widget\Widget|null
     */
    public function getWidget() {
        return $this->widget;
    }

    /**
     * Sets whether this row has been rendered
     * @param boolean $isRendered
     * @return null
     */
    public function setIsRendered($isRendered) {
        $this->isRendered = $isRendered;
    }

    /**
     * Checks if this row has been rendered
     * @return boolean
     */
    public function isRendered() {
        return $this->isRendered;
    }

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (isset($values[$this->name])) {
            $this->data = $values[$this->name];
        }
    }

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        $this->data = $data;

        if ($this->widget) {
            $this->widget->setValue($data);
        }
    }

    /**
     * Gets the data of this row
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Adds defined filters and validators to this row
     * @param ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    protected function addValidation(ValidationFactory $validationFactory) {
        $filters = $this->getOption(self::OPTION_FILTERS);
        if ($filters) {
            if (!is_array($filters)) {
                throw new FormException('Could not build the filters: no array provided in the ' . self::OPTION_FILTERS . ' option');
            }

            foreach ($filters as $name => $options) {
                if ($options instanceof Filter) {
                    $this->filters[] = $options;
                } else {
                    $this->filters[] = $validationFactory->createFilter($name, $options);
                }
            }
        }

        $validators = $this->getOption(self::OPTION_VALIDATORS);
        if ($validators) {
            if (!is_array($validators)) {
                throw new FormException('Could not build the validators: no array provided in the ' . self::OPTION_VALIDATORS . ' option');
            }

            foreach ($validators as $name => $options) {
                if ($options instanceof Validator) {
                    $this->validators[] = $options;
                } else {
                    $this->validators[] = $validationFactory->createValidator($name, $options);
                }
            }
        }
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $name = $this->getPropertyName($namePrefix);
        $id = $idPrefix . str_replace(']', '', str_replace('[', '-', str_replace('][', '-', $name)));

        if ($this->data !== null) {
            $default = $this->data;
        } else {
            $default = $this->getDefault();
        }

        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());
        $attributes['id'] = $id;
        if ($this->isDisabled()) {
            $attributes['disabled'] = 'disabled';
        } elseif ($this->isReadOnly()) {
            $attributes['readonly'] = 'readonly';
        }

        $this->processAttributes($attributes);

        $this->widget = $this->createWidget($name, $default, $attributes);
        $this->widget->setIsMultiple($this->getOption(self::OPTION_MULTIPLE, false));

        $this->addValidation($validationFactory);
    }

    /**
     * Processes the attributes before creating the widget
     * @param array $attributes Attributes by reference
     * @return null
     */
    protected function processAttributes(array &$attributes) {
        if ($this instanceof AbstractFormBuilderRow) {
            return;
        }

        $validators = $this->getOption(self::OPTION_VALIDATORS);
        if (!$validators) {
            return;
        }

        foreach ($validators as $name => $validator) {
            if ($name == 'required' || $validator instanceof RequiredValidator) {
                $attributes['required'] = 'required';

                break;
            }
        }
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        return new GenericWidget($this->type, $name, $default, $attributes);
    }

    /**
     * Applies the validation rules
     * @param ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        foreach ($this->filters as $filter) {
            $this->data = $filter->filter($this->data);
        }

        if (isset($this->widget)) {
            $this->widget->setValue($this->data);

            $name = $this->widget->getName();
        } else {
            $name = $this->name;
        }

        if ($this->getOption(self::OPTION_MULTIPLE)) {
            if (!is_array($this->data)) {
                $data = array($this->data);
            } else {
                $data = $this->data;
            }

            foreach ($data as $i => $d) {
                foreach ($this->validators as $validator) {
                    if (!$validator->isValid($d)) {
                        $validationException->addErrors($name . '[' . $i . ']', $validator->getErrors());
                    }
                }
            }
        } else {
            foreach ($this->validators as $validator) {
                if (!$validator->isValid($this->data)) {
                    $validationException->addErrors($name, $validator->getErrors());
                }
            }
        }
    }

    /**
     * Prepares the row for the form view
     * @return null
     */
    public function prepareForView() {

    }

}
