<?php

namespace ride\library\form\row;

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
     * Name of the HTML flag
     * @var string
     */
    const OPTION_HTML = 'html';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {

    }

}
