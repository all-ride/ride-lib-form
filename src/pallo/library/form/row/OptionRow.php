<?php

namespace pallo\library\form\row;

use pallo\library\form\widget\OptionWidget;
use pallo\library\validation\factory\ValidationFactory;

/**
 * Option row
 */
class OptionRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'option';

    /**
     * Option for the value decorator
     * @var string
     */
    const OPTION_DECORATOR = 'decorator';

    /**
     * Option for the select options
     * @var string
     */
    const OPTION_OPTIONS = 'options';

    /**
     * Option to set multiselect
     * @var string
     */
    const OPTION_MULTISELECT = 'multiselect';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (isset($values[$this->name])) {
            $data = $values[$this->name];

            if ($this->getOption(self::OPTION_MULTISELECT) && is_array($data)) {
                $newData = array();

                foreach ($data as $key => $value) {
                    $newData[$value] = $value;
                }

                $data = $newData;
            }

            $this->data = $data;
        } elseif (!$this->getOption(self::OPTION_OPTIONS) && !$this->getOption(self::OPTION_MULTISELECT)) {
            $this->data = false;
        } elseif ($this->getOption(self::OPTION_MULTISELECT)) {
            $this->data = array();
        }
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param pallo\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        parent::buildRow($namePrefix, $idPrefix, $validationFactory);

        $decorator = $this->getOption(self::OPTION_DECORATOR);
        if ($decorator) {
            $this->widget->setValue($decorator->decorate($this->widget->getValue()));
        }

        $this->widget = new OptionWidget($this->widget->getType(), $this->widget->getName(), $this->widget->getValue(), $this->widget->getAttributes());

        $options = $this->getOption(self::OPTION_OPTIONS);
        if ($options) {
            $this->widget->setOptions($options);
        }

        if ($this->getOption(self::OPTION_MULTISELECT)) {
            $this->widget->setIsMultiSelect(true);
        }
    }

}