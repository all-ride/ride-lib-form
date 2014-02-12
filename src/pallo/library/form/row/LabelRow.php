<?php

namespace pallo\library\form\row;

/**
 * Label row
 */
class LabelRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'label';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {

    }

}