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
     * Option to include seconds
     * @var string
     */
    const OPTION_SECONDS = 'seconds';

    /**
     * Option to include hours
     * @var string
     */
    const OPTION_HOURS = 'hours';

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        if (!is_numeric($data)) {
            return $data;
        }

        $hours = floor($data / 3600);
        $remaining = $data % 3600;
        $minutes = floor($remaining / 60);

        if ($this->getOption(self::OPTION_HOURS, true)) {
            $data = $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        } else {
            $minutes += $hours * 60;

            $data = $minutes;
        }

        if ($this->getOption(self::OPTION_SECONDS)) {
            $seconds = $remaining % 60;

            $data .= ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
        }

        parent::setData($data);
    }

    /**
     * Gets the data of this row
     * @return mixed
     */
    public function getData() {
        if ($this->data === null || $this->data === '' || strpos($this->data, ':') === false) {
            return null;
        }

        $time = 0;

        $tokens = explode(':', $this->data);

        if ($this->getOption(self::OPTION_HOURS, true)) {
            $time = ($tokens[0] * 3600) + ($tokens[1] * 60);
            if ($this->getOption(self::OPTION_SECONDS)) {
                $time += $tokens[2];
            }
        } else {
            $time = $tokens[0] * 60;
            if ($this->getOption(self::OPTION_SECONDS)) {
                $time += $tokens[1];
            }
        }

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
        $showHours = $this->getOption(self::OPTION_HOURS, true);
        $showSeconds = $this->getOption(self::OPTION_SECONDS);

        $placeHolder = '00';
        $regex = '';

        if ($showHours) {
            $placeHolder .= ':00';
            $regex .= '((([0-1])?[0-9])|(2[0-4]))';
            $regex .= ':[0-5][0-9]';
        } else {
            $regex .= '([0-9])*';
        }

        if ($showSeconds) {
            $placeHolder .= ':00';
            $regex . ':[0-5][0-9]';
        }

        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());

        if (isset($attributes['class'])) {
            $attributes['class'] .= ' time';
        } else {
            $attributes['class'] = 'time';
        }

        if (!isset($attributes['placeholder'])) {
            $attributes['placeholder'] = $placeHolder;
        }

        $this->setOption(self::OPTION_ATTRIBUTES, $attributes);

        $this->validators[] = $validationFactory->createValidator('regex', array(
            'required' => false,
            'regex' => '/' . $regex . '/',
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
