<?php

namespace ride\library\form\row;

use ride\library\validation\factory\ValidationFactory;

/**
 * Website row
 */
class WebsiteRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'website';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (!isset($values[$this->name])) {
            return;
        }

        $this->data = $values[$this->name];

        if ($this->data) {
            $start = substr($this->data, 0, 7);
            if ($start != 'http://' && $start != 'https:/') {
                $this->data = 'http://' . $this->data;
            }
        }
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $this->validators[] = $validationFactory->createValidator('website', array('required' => false));

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

}