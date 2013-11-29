<?php

namespace pallo\library\form\widget;

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
     * Sets the value for this widget
     * @param mixed $value
     * @return null
     */
    public function setValue($value);

    /**
     * Gets the value for this widget
     * @return mixed
     */
    public function getValue();

    /**
     * Gets the attributes for this widget
     * @return array
     */
    public function getAttributes();

}