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
        if (isset($attributes['required']) && $this->getOption(self::OPTION_MULTIPLE)) {
            unset($attributes['required']);
        }

        $default = $this->processWidgetValue($default);

        $options = $this->getOption(self::OPTION_OPTIONS);
        if ($options) {
            $reflectionHelper = $this->getReflectionHelper();

            $widgetOptions = array();

            $propertyValue = $this->getOption(self::OPTION_VALUE);
            if ($propertyValue) {
                foreach ($options as $index => $value) {
                    $widgetOptions[$reflectionHelper->getProperty($value, $propertyValue)] = $value;
                }

                $options = $widgetOptions;
            }

            $decorator = $this->getOption(self::OPTION_DECORATOR);
            $propertyLabel = $this->getOption(self::OPTION_PROPERTY);

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
        } else {
            $widgetOptions = array();
        }

        $widgetType = $this->getOption(self::OPTION_WIDGET);
        if (!$widgetType) {
            if (count($widgetOptions) < 7) {
                $widgetType = OptionRow::TYPE;
            } else {
                $widgetType = SelectRow::TYPE;
            }
        }

        $widget = new OptionWidget($widgetType, $name, $default, $attributes);
        $widget->setOptions($widgetOptions);

        return $widget;
    }

    /**
     * Processes the value of the row for the widget
     * @param mixed $value Value of the row
     * @param string Value for the widget
     */
    protected function processWidgetValue($value) {
        if (!$value) {
            return null;
        }

        if ($value && $this->getOption(self::OPTION_MULTIPLE) && !is_array($value)) {
            $value = array($value);
        }

        $propertyValue = $this->getOption(self::OPTION_VALUE);
        if ($propertyValue) {
            $reflectionHelper = $this->getReflectionHelper();

            if (is_array($value)) {
                foreach ($value as $index => $val) {
                    $value[$index] = $reflectionHelper->getProperty($val, $propertyValue);
                }
            } else {
                $value = $reflectionHelper->getProperty($value, $propertyValue);
            }
        }

        return $value;
    }

}
