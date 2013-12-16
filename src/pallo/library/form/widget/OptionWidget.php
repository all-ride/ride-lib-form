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

}