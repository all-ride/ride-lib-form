<?php

namespace ride\library\form\row;

use ride\library\validation\factory\ValidationFactory;

/**
 * Wysiwyg row
 */
class WysiwygRow extends TextRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'wysiwyg';

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' wysiwyg';
        } else {
            $attributes['class'] = 'wysiwyg';
        }
        $this->setOption(self::OPTION_ATTRIBUTES, $attributes);

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

}