<?php

namespace pallo\library\form;

use pallo\library\form\exception\FormException;
use pallo\library\form\row\factory\RowFactory;
use pallo\library\form\view\GenericView;
use pallo\library\reflection\ReflectionHelper;
use pallo\library\validation\constraint\Constraint;
use pallo\library\validation\constraint\GenericConstraint;
use pallo\library\validation\factory\ValidationFactory;
use pallo\library\validation\exception\ValidationException;

/**
 * Abstract implementation of a form
 */
abstract class AbstractForm implements Form {

    /**
     * Object factory
     * @var pallo\library\reflection\ReflectionHelper
     */
    protected $reflectionHelper;

    /**
     * Data of this form
     * @var mixed
     */
    protected $data;

    /**
     * Row definitions
     * @var array
     */
    protected $rows;

    /**
     * Validation exception of the last validate call
     * @var pallo\library\validation\exception\ValidationException
     */
    protected $validationException;

    /**
     * Instance of the validation factory
     * @var pallo\library\validation\factory\ValidationFactory
     */
    protected $validationFactory;

    /**
     * Constructs a new form
     * @return null
     */
    public function __construct(ReflectionHelper $reflectionHelper) {
        $this->reflectionHelper = $reflectionHelper;
        $this->rowFactory = null;

        $this->id = 'form';
        $this->data = null;
        $this->rows = array();

        $this->isSubmitted = false;
        $this->dataNeedsProcessing = false;
        $this->request = null;
        $this->validationException = null;
    }

    /**
     * Sets the id of this form, also used for suffixes for field ids
     * @param string $id Id of the form
     * @return null
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the id of this form, also used for suffixes for field ids
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the row factory to this form
     * @param pallo\library\form\row\factory\RowFactory $rowFactory
     * @return null
     */
    public function setRowFactory(RowFactory $rowFactory) {
        $this->rowFactory = $rowFactory;
    }

    /**
     * Sets the validation factory
     * @param pallo\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function setValidationFactory(ValidationFactory $validationFactory) {
        $this->validationFactory = $validationFactory;
    }

    /**
     * Adds a row to the form
     * @param string $name Name of the row
     * @param string $type Type of the row
     * @param array $options Extra options for the row
     * @return null
     */
    public function addRow($name, $type, array $options = array()) {
        if (!$this->rowFactory) {
            throw new FormException('Could not add a row to the form: no RowFactory set');
        }

        $this->rows[$name] = $this->rowFactory->createRow($type, $name, $options);
    }

    /**
     * Removes a row from the form
     * @param string $name Name of the row
     * @return null
     */
    public function removeRow($name) {
        if (!isset($this->rows[$name])) {
            throw new FormException('Could not remove row with name "' . $name . '": row not set');
        }

        unset($this->rows[$name]);
    }

    /**
     * Gets a row from the form
     * @param string $name Name of the row
     * @return Row
     */
    public function getRow($name) {
        if (!isset($this->rows[$name])) {
            throw new FormException('Could not get row with name "' . $name . '": row not set');
        }

        return $this->rows[$name];
    }

    /**
     * Gets the rows
     * @return \pallo\library\html\form\builder\Row
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Validates this form
     * @return null
     * @throws pallo\library\validation\exception\ValidationException when the
     * data on the form could not be validated
     */
    public function validate() {
        $this->validationException = new ValidationException();

        foreach ($this->rows as $name => $row) {
            $row->applyValidation($this->validationException);
        }

        if ($this->validationException->hasErrors()) {
            $this->dataNeedsProcessing = false;

            throw $this->validationException;
        }

        $this->dataNeedsProcessing = true;
    }

    /**
     * Sets the validation exception
     * @param pallo\library\validation\exception\ValidationException $exception
     * @return null
     */
    public function setValidationException(ValidationException $exception) {
        $this->validationException = $exception;
    }

    /**
     * Gets the validation exception of the last validate call
     * @return pallo\library\validation\exception\ValidationException|null
     */
    public function getValidationException() {
        if (!$this->validationException) {
            $this->validationException = new ValidationException();
        }

        return $this->validationException;
    }

    /**
     * Sets the data to this form
     * @param mixed $data Data for this form
     * @return null
     */
    public function setData($data) {
        foreach ($this->rows as $name => $row) {
            $row->setData($this->reflectionHelper->getProperty($data, $name));
        }

        $this->data = $data;
    }

    /**
     * Gets the data from this form
     * @return mixed Data of this form
     */
    public function getData() {
        if (!$this->dataNeedsProcessing) {
            return $this->data;
        }

        $values = array();
        foreach ($this->rows as $name => $row) {
            $values[$name] = $row->getData();
        }

        if ($this->data === null) {
            $this->data = $this->createData($values);
        } else {
            foreach ($values as $name => $value) {
                $this->reflectionHelper->setProperty($this->data, $name, $value);
            }
        }

        $this->dataNeedsProcessing = false;

        return $this->data;
    }

    /**
     * Creates the data for this form
     * @param array $values Row values
     * @return mixed
     */
    protected function createData(array $values) {
        return $values;
    }

    /**
     * Checks if this form is submitted
     * @return boolean
     */
    public function isSubmitted() {
        return $this->isSubmitted;
    }

    /**
     * Build the rows
     * @param array $data Initial data of the rows
     * @throws pallo\library\form\exception\FormException when no validation
     * factory set
     */
    protected function buildRows(array $data = null) {
        if (!$this->validationFactory) {
            throw new FormException('Could not build the form: no validation factory set');
        }

        $namePrefix = '';
        $idPrefix = $this->getId() . '-';

        foreach ($this->rows as $name => $row) {
            if ($this->data !== null) {
                $row->setData($this->reflectionHelper->getProperty($this->data, $name));
            }

            if ($data !== null) {
                $row->processData($data);
            }

            $row->buildRow($namePrefix, $idPrefix, $this->validationFactory);
        }

        if ($data !== null) {
            $this->isSubmitted = true;
            $this->dataNeedsProcessing = true;
        } else {
            $this->isSubmitted = false;
        }
    }

    /**
     * Gets a viewable and serializable version of this form
     * @return pallo\library\form\view\View
     */
    public function getView() {
        $id = $this->getId();
        $data = $this->getData();
        $validationException = $this->getValidationException();

        $rows = $this->getRows();
        foreach ($rows as $row) {
            $row->prepareForView();
        }

        return new GenericView($id, $data, $rows, $validationException->getAllErrors());
    }

}