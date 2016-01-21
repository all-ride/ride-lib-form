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
     * Option for the label decorator
     * @var string
     */
    const OPTION_DECORATOR = 'decorator';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {

    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return \ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        $decorator = $this->getOption(self::OPTION_DECORATOR);
        if ($decorator) {
            $default = $decorator->decorate($default);
        }

        if (is_array($default)) {
            $default = implode(', ', $default);
        }

        return parent::createWidget($name, $default, $attributes);
    }

}
