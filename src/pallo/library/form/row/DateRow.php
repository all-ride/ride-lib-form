<?php

namespace pallo\library\form\row;

use pallo\library\validation\exception\ValidationException;
use pallo\library\validation\factory\ValidationFactory;
use pallo\library\validation\ValidationError;

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
     * @throws pallo\library\form\row\ValidationException
     */
    protected function parseValue($value) {
        if (!$value || is_numeric($value)) {
            return $value;
        }

        if (strpos($value, ' ') !== false) {
            list($value, $time) = explode(' ', $value, 2);
        }

        try {
            $value = DateTime::createFromFormat($this->getFormat(), $value);
            $value = $value->getTimestamp();
        } catch (Exception $e) {
            $error = new ValidationError('error.date.format', '%value% is not in the right format', array('value' => $value));

            $exception = new ValidationException();
            $exception->addErrors($this->getName(), array($error));

            throw $exception;
        }

        return $value;
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

        $value = $this->widget->getValue();
        if ($value && is_numeric($value)) {
            $this->widget->setValue(date($this->getFormat(), $value));
        }
    }

}