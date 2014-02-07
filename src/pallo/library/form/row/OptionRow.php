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
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (isset($values[$this->name])) {
            $data = $values[$this->name];

            if ($this->getOption(self::OPTION_MULTIPLE)) {
                $newData = array();

                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        $newData[$value] = $value;
                    }
                }

                $data = $newData;
            }

            $this->data = $data;
        } elseif (!$this->getOption(self::OPTION_OPTIONS) && !$this->getOption(self::OPTION_MULTIPLE)) {
            $this->data = null;
        } elseif ($this->getOption(self::OPTION_MULTIPLE)) {
            $this->data = array();
        }
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return pallo\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        $decorator = $this->getOption(self::OPTION_DECORATOR);
        if ($decorator) {
            $default = $decorator->decorate($default);
        }

        $widget = new OptionWidget($this->type, $name, $default, $attributes);

        $options = $this->getOption(self::OPTION_OPTIONS);
        if ($options) {
            $widget->setOptions($options);
        }

        return $widget;
    }


}