<?php

namespace ride\library\form\row;

use ride\library\form\widget\OptionWidget;
use ride\library\reflection\ReflectionHelper;
use ride\library\validation\exception\ValidationException;

/**
 * Object row
 */
class ObjectRow extends OptionRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'object';

    /**
     * Option for the value property
     * @var string
     */
    const OPTION_VALUE = 'value';

    /**
     * Option for the label property
     * @var string
     */
    const OPTION_PROPERTY = 'property';

    /**
     * Option for the label decorator
     * @var string
     */
    const OPTION_DECORATOR = 'decorator';

    /**
     * Instance of the reflection helper
     * @var \ride\library\reflection\ReflectionHelper
     */
    protected $reflectionHelper;

    /**
     * Sets the reflection helper
     * @param \ride\library\reflection\ReflectionHelper $reflectionHelper
     * @return null
     */
    public function setReflectionHelper(ReflectionHelper $reflectionHelper) {
        $this->reflectionHelper = $reflectionHelper;
    }

    /**
     * Gets the reflection helper
     * @return \ride\library\reflection\ReflectionHelper
     */
    public function getReflectionHelper() {
        if (!$this->reflectionHelper) {
            $this->reflectionHelper = new ReflectionHelper();
        }

        return $this->reflectionHelper;
    }

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
                        $newData[$value] = isset($options[$value]) ? $options[$value] : $value;
                    }
                }

                $this->data = $newData;
            } elseif (isset($options[$data])) {
                $this->data = $options[$data];
            } else {
                $this->data = null;
            }
        } elseif (!$options && !$isMultiple) {
            $this->data = null;
        } elseif ($isMultiple) {
            $this->data = array();
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
        $reflectionHelper = $this->getReflectionHelper();
        $decorator = $this->getOption(self::OPTION_DECORATOR);

        $propertyValue = $this->getOption(self::OPTION_VALUE);
        $propertyLabel = $this->getOption(self::OPTION_PROPERTY);

        if ($default && $this->getOption(self::OPTION_MULTIPLE) && !is_array($default)) {
            $default = array($default);
        }

        if ($default && $propertyValue) {
            if (is_array($default)) {
                foreach ($default as $defaultIndex => $defaultValue) {
                    $default[$defaultIndex] = $reflectionHelper->getProperty($defaultValue, $propertyValue);
                }
            } else {
                $default = $reflectionHelper->getProperty($default, $propertyValue);
            }
        }

        if (isset($attributes['required']) && $this->getOption(self::OPTION_MULTIPLE)) {
            unset($attributes['required']);
        }

        $widget = new OptionWidget(SelectRow::TYPE, $name, $default, $attributes);

        $options = $this->getOption(self::OPTION_OPTIONS);
        if ($options) {
            $widgetOptions = array();

            if ($decorator) {
                foreach ($options as $index => $value) {
                    $widgetOptions[$index] = $decorator->decorate($value);
                }
            } elseif ($propertyLabel) {
                foreach ($options as $index => $value) {
                    $widgetOptions[$index] = $reflectionHelper->getProperty($value, $propertyLabel);
                }
            } else {
                $widgetOptions = $options;
            }

            $widget->setOptions($widgetOptions);
        }

        return $widget;
    }

}
