<?php

namespace pallo\library\form\row;

use pallo\library\form\component\Component;
use pallo\library\form\exception\FormException;
use pallo\library\validation\exception\ValidationException;
use pallo\library\validation\factory\ValidationFactory;

/**
 * Row for a collection of rows
 */
class CollectionRow extends AbstractFormBuilderRow {

    /**
     * Type of this row
     * @var string
     */
    const TYPE = 'collection';

    /**
     * Option for the type of collection
     * @var string
     */
    const OPTION_TYPE = 'type';

    /**
     * Option for the row options
     * @var string
     */
    const OPTION_OPTIONS = 'options';

    /**
     * Key for the prototype value
     * @var string
     */
    const VALUE_PROTOTYPE = '%prototype%';

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (!isset($values[$this->name])) {
            return;
        } elseif (!is_array($values[$this->name])) {
            $this->data = array();
        } else {
            $this->data = $values[$this->name];
        }
    }

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        if ($data === null) {
            $data = array();
        } elseif (!is_array($data)) {
            $type = gettype($data);
            if ($type == 'object') {
                $type = get_class($data);
            }

            throw new FormException('Could not set the data for this row: no array but ' . $type . ' provided');
        }

        $this->data = $data;
    }

    /**
     * Gets the data of this row
     * @param pallo\library\http\Request
     * @return mixed
     */
    public function getData() {
        if (!$this->data) {
            return array();
        }

        if (!$this->widget) {
            foreach ($this->rows as $name => $row) {
                if ($name === self::VALUE_PROTOTYPE) {
                    continue;
                }

                $this->data[$name] = $row->getData();
            }
        }

        return $this->data;
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param pallo\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $name = $this->getPropertyName($namePrefix);

        $type = $this->getOption(self::OPTION_TYPE);
        if (!$type) {
            throw new FormException('Could not build ' . $name . ': no type option provided');
        }

        $this->addValidation($validationFactory);

        if ($type !== ComponentRow::TYPE) {
            $id = $idPrefix . str_replace('[', '-', str_replace('][', '-', $name));

            $row = $this->rowFactory->createRow($type, $name, $this->options);
            $row->setData($this->data);
            $row->buildRow('', $idPrefix, $validationFactory);

            $this->widget = $row->getWidget();
            $this->widget->setAttribute('id', $id);
            $this->widget->setIsMultiple(true);

            return;
        }

        $options = $this->getOption(self::OPTION_OPTIONS, array());

        if ($this->data) {
            $data = $this->data;
        } else {
            $data = array();
        }
        $data[self::VALUE_PROTOTYPE] = null;

        foreach ($data as $key => $value) {
            $namePrefix = $name . '[' . $key . '][';

            $row = $this->rowFactory->createRow($type, $name, $options);

            if (is_array($value)) {
                $row->processData($value);
            } else {
                $row->setData($value);
            }

            $row->buildRow($namePrefix, $idPrefix, $validationFactory);

            $this->rows[$key] = $row;
        }
    }

    /**
     * Applies the validation rules
     * @param pallo\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        $type = $this->getOption(self::OPTION_TYPE);

        if ($type !== ComponentRow::TYPE) {
            parent::applyValidation($validationException);

            return;
        }

        foreach ($this->rows as $key => $row) {
            if ($key === self::VALUE_PROTOTYPE) {
                continue;
            }

            $row->applyValidation($validationException);
        }
    }

}