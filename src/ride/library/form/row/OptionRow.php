<?php

namespace ride\library\form\row;

use ride\library\form\widget\OptionWidget;
use ride\library\validation\exception\ValidationException;

/**
 * Option row
 */
class OptionRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'option';

    /**
     * Option for the select options
     * @var string
     */
    const OPTION_OPTIONS = 'options';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        $options = $this->getOption(self::OPTION_OPTIONS);
        $isMultiple = $this->getOption(self::OPTION_MULTIPLE);

        if (isset($values[$this->name])) {
            $data = $values[$this->name];

            if ($isMultiple) {
                $newData = array();

                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        $newData[$value] = $value;
                    }
                }

                $data = $newData;
            }

            $this->data = $data;
        } elseif (!$options && !$isMultiple) {
            $this->data = null;
        } elseif ($isMultiple) {
            $this->data = array();
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
            $this->widget->setValue($this->processWidgetValue($data));
        }
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return \ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        if (isset($attributes['required']) && $this->getOption(self::OPTION_MULTIPLE)) {
            unset($attributes['required']);
        }

        $default = $this->processWidgetValue($default);

        $widget = new OptionWidget($this->type, $name, $default, $attributes);

        $options = $this->getOption(self::OPTION_OPTIONS);
        if ($options) {
            $widget->setOptions($options);
        }

        return $widget;
    }

    /**
     * Applies the validation rules
     * @param \ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        foreach ($this->filters as $filter) {
            $this->data = $filter->filter($this->data);
        }

        if (isset($this->widget)) {
            $this->widget->setValue($this->processWidgetValue($this->data));

            $name = $this->widget->getName();
        } else {
            $name = $this->name;
        }

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($this->data)) {
                $validationException->addErrors($name, $validator->getErrors());
            }
        }
    }

    /**
     * Processes the value of the row for the widget
     * @param mixed $value Value of the row
     * @param string Value for the widget
     */
    protected function processWidgetValue($value) {
        return $value;
    }

}
