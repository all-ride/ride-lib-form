<?php

namespace pallo\library\form\widget;

/**
 * Date widget for a form row
 */
class DateWidget extends GenericWidget {

    /**
     * Constructs a new widget
     * @param string $type Type of the widget
     * @param string $name Name of the row
     * @param mixed $value Value for the widget
     * @param array $attributes Extra attributes
     * @return null
     */
    public function __construct($name, $value = null, array $attributes = array(), $isMultiple = false) {
        parent::__construct('date', $name, $value, $attributes, $isMultiple);
    }

    /**
     * Sets the value for this widget
     * @param mixed $value Value to set
     * @param string $part Name of the part
     * @return null
     */
    public function setValue($value, $part = null) {
        if (is_numeric($value)) {
            $value = date($this->attributes['data-format-php'], $value);
        }

        if ($part !== null) {
            $this->value[$part] = $value;
        } else {
            $this->value = $value;
        }
    }

}