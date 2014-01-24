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
    protected $options = null;

    /**
     * Sets the available options
     * @param array $options
     * @return null
     */
    public function setOptions(array $options) {
        if ($options) {
            $this->options = $options;
        } else {
            $this->options = null;
        }
    }

    /**
     * Gets the available options
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

}