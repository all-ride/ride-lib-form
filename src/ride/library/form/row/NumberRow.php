<?php

namespace ride\library\form\row;

/**
 * Number row
 */
class NumberRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'number';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        parent::processData($values);

        if (!is_numeric($this->data)) {
            $this->setData(0);
        }
    }

}
