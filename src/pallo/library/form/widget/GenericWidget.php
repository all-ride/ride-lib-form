<?php

namespace pallo\library\form\widget;

/**
 * Generic widget for a form row
 */
class GenericWidget implements Widget {

    /**
     * Type of the widget
     * @var string
     */
    protected $type;

    /**
     * Name of the row
     * @var string
     */
    protected $name;

    /**
     * Value for the widget
     * @var mixed
     */
    protected $value;

    /**
     * Extra attributes for the widget
     * @var array
     */
    protected $attributes;

    /**
     * Flag to see if this widget contains an array value
     * @var boolean
     */
    protected $isMultiple;

    /**
     * Constructs a new widget
     * @param string $type Type of the widget
     * @param string $name Name of the row
     * @param mixed $value Value for the widget
     * @param array $attributes Extra attributes
     * @return null
     */
    public function __construct($type, $name, $value = null, array $attributes = array(), $isMultiple = false) {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->isMultiple = $isMultiple;
    }

    /**
     * Gets the type of this widget
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the name for this widget
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets whether this widget contains an array value
     * @param boolean $isMultiple
     * @return null
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = $isMultiple;
    }

    /**
     * Gets whether this widget contains an array value
     * @return boolean
     */
    public function isMultiple() {
        return $this->isMultiple;
    }

    /**
     * Sets the value for this widget
     * @param mixed $value Value to set
     * @param string $part Name of the part
     * @return null
     */
    public function setValue($value, $part = null) {
        if ($part !== null) {
            $this->value[$part] = $value;
        } else {
            $this->value = $value;
        }
    }

    /**
     * Gets the value for this widget
     * @param string $part Name of the part
     * @return mixed
    */
    public function getValue($part = null) {
        if ($part !== null) {
            if (is_array($this->value) && isset($this->value[$part])) {
                return $this->value[$part];
            } else {
                return null;
            }
        } else {
            return $this->value;
        }
    }

    /**
     * Sets an attribute
     * @param string $key Name of the attribute
     * @param mixed $value Value of the attribute
     * @return null
     */
    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * Gets the attributes for this widget
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Gets the id of this widget
     * @return string
     */
    public function getId() {
        return $this->attributes['id'];
    }

}