<?php

namespace ride\library\form\row;

use ride\library\validation\factory\ValidationFactory;

/**
 * Email row
 */
class EmailRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'email';

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $this->validators[] = $validationFactory->createValidator('email', array('required' => false));

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

}