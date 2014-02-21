<?php

namespace ride\library\form\row;

use ride\library\form\row\util\JQueryDateFormatConverter;
use ride\library\form\widget\DateWidget;
use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;
use ride\library\validation\ValidationError;

use \DateTime;
use \Exception;

/**
 * Date row
 */
class DateRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'date';

    /**
     * Default date format
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * Option for the date format
     * @var string
     */
    const OPTION_FORMAT = 'format';

    /**
     * Gets the date format
     * @return string
     */
    public function getFormat() {
        return $this->getOption(self::OPTION_FORMAT, self::DATE_FORMAT);
    }

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (isset($values[$this->name])) {
            $this->data = $this->parseValue($values[$this->name]);
        }
    }

    /**
     * Parses a formatted date into a timestamp
     * @param string $value
     * @throws ride\library\form\row\ValidationException
     */
    protected function parseValue($value) {
        if (!$value || is_numeric($value)) {
            return $value;
        }

        if (strpos($value, ' ') !== false) {
            list($value, $time) = explode(' ', $value, 2);
        }

        try {
            $originalValue = $value;

            $value = DateTime::createFromFormat($this->getFormat(), $value);
            if ($value === false) {
                throw new Exception();
            }

            $value = $value->getTimestamp();
        } catch (Exception $e) {
            $error = new ValidationError('error.validation.date.format', '%value% is not in the right format', array('value' => $originalValue));

            $exception = new ValidationException();
            $exception->addErrors($this->getName(), array($error));

            throw $exception;
        }

        return $value;
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        $dateConverter = new JQueryDateFormatConverter();
        $format = $this->getFormat();

        $attributes['data-format-php'] = $format;
        $attributes['data-format-jquery'] = $dateConverter->convertFormatFromPhp($format);

        return new DateWidget($name, $default, $attributes);
    }

}