<?php

namespace pallo\library\form\row;

use pallo\library\validation\factory\ValidationFactory;

/**
 * Select row
 */
class SelectRow extends OptionRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'select';

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function processData(array $values) {
        if (!isset($values[$this->name])) {
            return;
        }

        $data = $values[$this->name];

        if ($this->getOption(self::OPTION_MULTISELECT) && is_array($data)) {
            $newData = array();

            foreach ($data as $key => $value) {
                $newData[$value] = $value;
            }

            $data = $newData;
        }

        $this->data = $data;
    }

}