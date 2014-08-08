<?php

namespace ride\library\form\row;

use ride\library\form\widget\GenericWidget;
use ride\library\validation\factory\ValidationFactory;

/**
 * Time row
 */
class TimeRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'time';

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        if (is_numeric($data)) {
            $hours = floor($data / 3600);
            $minutes = floor(($data % 3600) / 60);

            $data = $hours . ':' . str_pad($minutes, 2, '0');
        }

        parent::setData($data);
    }

    /**
     * Gets the data of this row
     * @return mixed
     */
    public function getData() {
        if ($this->data === null || $this->data === '') {
            return null;
        }

        list($hours, $minutes) = explode(':', $this->data);

        $time = ($hours * 3600) + ($minutes * 60);

        return $time;
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $this->validators[] = $validationFactory->createValidator('regex', array(
            'required' => false,
            'regex' => '/([0-2])?[0-9]:[0-5][0-9]/',
            'error.regex' => 'error.validation.time',
        ));

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return \ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        return new GenericWidget('string', $name, $default, $attributes);
    }

}
