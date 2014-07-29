<?php

namespace ride\library\form\row;

/**
 * Button row
 */
class ButtonRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'button';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (isset($values[$this->name])) {
            $this->data = true;
        } else {
            $this->data = false;
        }
    }

}
