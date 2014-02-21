<?php

namespace ride\library\form\row;

use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;
use ride\library\validation\validator\ImageValidator;

/**
 * Image row
 */
class ImageRow extends FileRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'image';

    /**
     * Applies the validation rules
     * @param ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        foreach ($this->filters as $filter) {
            $this->data = $filter->filter($this->data);
        }

        if (isset($this->widget)) {
            $this->widget->setValue($this->data);

            $name = $this->widget->getName();
        } else {
            $name = $this->name;
        }

        if ($this->getOption(self::OPTION_MULTIPLE)) {
            if (!is_array($this->data)) {
                $data = array($this->data);
            } else {
                $data = $this->data;
            }

            foreach ($data as $i => $d) {
                foreach ($this->validators as $validator) {
                    if (!$validator->isValid($d)) {
                        $validationException->addErrors($name . '[' . $i . ']', $validator->getErrors());
                    }
                }

                if ($validationException->hasErrors($name . '[' . $i . ']')) {
                    $data[$i] = null;
                }
            }

            $this->data = $data;

            if (isset($this->widget)) {
                $this->widget->setValue($data);
            }
        } else {
            foreach ($this->validators as $validator) {
                if (!$validator->isValid($this->data)) {
                    $validationException->addErrors($name, $validator->getErrors());
                }
            }

            if ($validationException->hasErrors($name)) {
                $this->data = null;

                if (isset($this->widget)) {
                    $this->widget->setValue(null);
                }
            }
        }
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $found = false;

        $validators = $this->getOption(self::OPTION_VALIDATORS);
        if ($validators) {
            foreach ($validators as $validatorName => $validator) {
                if ($validatorName === 'image' || $validator instanceof ImageValidator) {
                    $found = true;

                    break;
                }
            }
        }

        if (!$found) {
            $this->validators[] = $validationFactory->createValidator('image', array('required' => false));
        }

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

}