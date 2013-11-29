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
     * Constructs a new widget
     * @param string $type Type of the widget
     * @param string $name Name of the row
     * @param mixed $value Value for the widget
     * @param array $attributes Extra attributes
     * @return null
     */
    public function __construct($type, $name, $value = null, array $attributes = array()) {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
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
     * Sets the value for this widget
     * @param mixed $value
     * @return null
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Gets the value for this widget
     * @return mixed
     */
    public function getValue() {
        return $this->value;
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