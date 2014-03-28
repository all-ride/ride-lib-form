<?php

namespace ride\library\form\row;

use ride\library\form\exception\FormException;
use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;

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
     * Validation errors occured during build
     * @var array
     */
    protected $validationErrors;

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        $this->oldData = $this->data;

        if (!isset($values[$this->name]) || !is_array($values[$this->name])) {
            $this->data = array();
        } else {
            $this->data = $values[$this->name];
        }

        $this->processedData = $values;
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
     * @return mixed
     */
    public function getData() {
        if (!$this->data) {
            return array();
        }

        if (!$this->widget && $this->rows) {
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
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $name = $this->getPropertyName($namePrefix);

        $type = $this->getOption(self::OPTION_TYPE);
        if (!$type) {
            throw new FormException('Could not build ' . $name . ': no type option provided');
        }

        $this->addValidation($validationFactory);

        $options = $this->getOption(self::OPTION_OPTIONS, array());

        if ($type !== ComponentRow::TYPE) {
            $id = $idPrefix . str_replace('[', '-', str_replace('][', '-', $name));

            $options[self::OPTION_MULTIPLE] = true;

            $this->rows[$name] = $this->rowFactory->createRow($type, $name, $options);
            if (isset($this->processedData)) {
                $this->rows[$name]->setData($this->oldData);
                $this->rows[$name]->processData($this->processedData);

                $this->data = $this->rows[$name]->getData();

                unset($this->oldData);
                unset($this->processedData);
            } else {
                $this->rows[$name]->setData($this->data);
            }

            $this->rows[$name]->buildRow('', $idPrefix, $validationFactory);

            $this->widget = $this->rows[$name]->getWidget();
            $this->widget->setAttribute('id', $id);
            $this->widget->setIsMultiple(true);

            return;
        }

        if ($this->data) {
            $data = $this->data;
        } else {
            $data = array();
        }
        $data[self::VALUE_PROTOTYPE] = null;

        if (isset($options[ComponentRow::OPTION_COMPONENT])) {
            $component = $options[ComponentRow::OPTION_COMPONENT];
        } else {
            $component = null;
        }

        $this->validationErrors = array();

        foreach ($data as $key => $value) {
            $namePrefix = $name . '[' . $key . '][';

            if (is_object($component)) {
                $options['component'] = clone $component;
            }

            $options[ComponentRow::OPTION_EMBED] = true;

            $row = $this->rowFactory->createRow($type, $name, $options);

            if ($key !== self::VALUE_PROTOTYPE && isset($this->processedData)) {
                if (isset($this->oldData[$key])) {
                    $row->setData($this->oldData[$key]);
                }

                try {
                    $row->processData($value);
                } catch (ValidationException $exception) {
                    $errors = $exception->getAllErrors();
                    foreach ($errors as $fieldName => $fieldErrors) {
                        $this->validationsErrors[$fieldName][] = $fieldErrors;
                    }
                }


                $this->data[$key] = $row->getData();
            } else {
                $row->setData($value);
            }

            $row->buildRow($namePrefix, $idPrefix, $validationFactory);

            $this->rows[$key] = $row;
        }

        unset($this->oldData);
        unset($this->processedData);
    }

    /**
     * Applies the validation rules
     * @param \ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        // apply validation on inner rows
        $type = $this->getOption(self::OPTION_TYPE);
        if ($type !== ComponentRow::TYPE) {
            parent::applyValidation($validationException);
        } else {
            foreach ($this->rows as $key => $row) {
                if ($key === self::VALUE_PROTOTYPE) {
                    continue;
                }

                $row->applyValidation($validationException);
            }
        }

        // update the data
        $this->data = $this->getData();

        // apply validation on collection
        foreach ($this->filters as $filter) {
            $this->data = $filter->filter($this->data);
        }

        if (isset($this->widget)) {
            $this->widget->setValue($this->data);

            $name = $this->widget->getName();
        } else {
            $name = $this->name;
        }

        if (!is_array($this->data)) {
            $this->data = (array) $this->data;
        }

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($this->data)) {
                $validationException->addErrors($name, $validator->getErrors());
            }
        }

        if (!$this->validationErrors) {
            return;
        }

        foreach ($this->validationErrors as $fieldName => $fieldErrorContainers) {
            foreach ($fieldErrorContainers as $fieldErrors) {
                $validationException->addErrors($fieldName, $fieldErrors);
            }
        }
    }

}