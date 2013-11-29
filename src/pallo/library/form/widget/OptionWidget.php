<?php

namespace pallo\library\form\widget;

/**
 * Option widget for a form row
 */
class OptionWidget extends GenericWidget {

    /**
     * Option values for the widget
     * @var array
     */
    protected $options = array();

    /**
     * Flag to see if this widget has multi select
     * @var boolean
     */
    protected $isMultiSelect = false;

    /**
     * Sets the available options
     * @param array $options
     * @return null
     */
    public function setOptions(array $options) {
        $this->options = $options;
    }

    /**
     * Gets the available options
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Sets whether this widget can select multiple items
     * @param boolean $flag
     * @return null
     */
    public function setIsMultiSelect($flag) {
        $this->isMultiSelect = $flag;
    }

    /**
     * Gets whether this widget can select multiple items
     * @return boolean
     */
    public function isMultiSelect() {
        return $this->isMultiSelect;
    }

}