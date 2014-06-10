<?php

namespace ride\library\form\widget;

/**
 * Widget for a form row
 */
interface Widget {

    /**
     * Gets the type of this widget
     * @return string
     */
    public function getType();

    /**
     * Gets the name for this widget
     * @return string
     */
    public function getName();

    /**
     * Sets whether this widget contains an array value
     * @param boolean $isMultiple
     * @return null
     */
    public function setIsMultiple($isMultiple);

    /**
     * Gets whether this widget contains an array value
     * @return boolean
     */
    public function isMultiple();

    /**
     * Sets whether this widget is required
     * @param boolean $isRequired
     * @return null
     */
    public function setIsRequired($isRequired);

    /**
     * Gets whether this widget is required
     * @return boolean
     */
    public function isRequired();

    /**
     * Sets the value for this widget
     * @param mixed $value Value to set
     * @param string $part Name of the part
     * @return null
     */
    public function setValue($value, $part = null);

    /**
     * Gets the value for this widget
     * @param string $part Name of the part
     * @return mixed
     */
    public function getValue($part = null);

    /**
     * Gets the attributes for this widget
     * @return array
     */
    public function getAttributes();

}
