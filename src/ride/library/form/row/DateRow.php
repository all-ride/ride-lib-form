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
     * Option to trim the time
     * @var string
     */
    const OPTION_ROUND = 'round';

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
     * @throws \ride\library\form\row\ValidationException
     */
    protected function parseValue($value) {
        if (!$value) {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }

        try {
            $originalValue = $value;

            $result = DateTime::createFromFormat($this->getFormat(), $value);
            if ($result === false) {
                throw new Exception();
            }

            $result = $result->getTimestamp();
        } catch (Exception $e) {
            $error = new ValidationError('error.validation.date.format', '%value% is not in the right format', array('value' => $originalValue));

            $exception = new ValidationException();
            $exception->addErrors($this->getName(), array($error));

            $this->data = $value;

            throw $exception;
        }

        if ($this->getOption(self::OPTION_ROUND)) {
            $result -= ($result + date('Z', $result)) % 86400;
        }

        return $result;
    }

    /**
     * Adds defined filters and validators to this row
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    protected function addValidation(ValidationFactory $validationFactory) {
        parent::addValidation($validationFactory);

        $this->validators[] = $validationFactory->createValidator('numeric', array(
            'required' => false,
            'error.numeric' => 'error.validation.date.format',
        ));
    }

    /**
     * Creates the widget for this row
     * @param string $name
     * @param mixed $default
     * @param array $attributes
     * @return \ride\library\form\widget\Widget
     */
    protected function createWidget($name, $default, array $attributes) {
        $format = $this->getFormat();

        $dateConverter = new JQueryDateFormatConverter();

        $attributes['data-format-php'] = $format;
        $attributes['data-format-jquery'] = $dateConverter->convertFormatFromPhp($format);

        return new DateWidget('date', $name, $default, $attributes);
    }

}
