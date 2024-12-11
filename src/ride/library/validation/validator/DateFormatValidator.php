<?php

namespace ride\library\validation\validator;

use ride\library\form\row\DateRow;
use ride\library\validation\exception\ValidationException;
use ride\library\validation\ValidationError;
use DateTime;

class DateFormatValidator extends AbstractValidator
{
    /**
     * Machine name of this validator
     * @var string
     */
    const NAME = 'date_format';

    const OPTION_DATE_FORMAT = 'format';

    const OPTION_DATE_ROW = 'date_row';

    private $dateFormat = 'd/m/Y';
    /**
     * @var DateRow
     */
    private $dateRow;

    public function __construct(array $options = null)
    {
        parent::__construct($options);

        if(array_key_exists(self::OPTION_DATE_FORMAT, $options)) {
            $this->dateFormat = $options[self::OPTION_DATE_FORMAT];
        }

        if(array_key_exists(self::OPTION_DATE_ROW, $options)) {
            $this->dateRow = $options[self::OPTION_DATE_ROW];
        }
    }

    /**
     * Checks whether a date string is wellformatted according to the given date format
     * @param mixed $value
     * @return boolean true if the value is a valid value, false otherwise
     */
    public function isValid($value) {
        $processedDate = (new DateTime())->setTimestamp($value)->format($this->dateFormat);
        if ($value && $this->dateRow && $this->dateRow->getFormDate() && $this->dateRow->getFormDate() !== $processedDate) {
            $this->addValidationError('error.validation.date.format',
                'error.validation.date.format',
                [
                    'value' => $this->dateRow->getFormDate()
                ]);
            return false;
        }

        return true;
    }
}
