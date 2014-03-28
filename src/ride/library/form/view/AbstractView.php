<?php

namespace ride\library\form\view;

use ride\library\form\exception\FormException;

/**
 * Abstract implementation of a form view
 */
abstract class AbstractView implements View {

    /**
     * Id of this form
     * @var string
     */
    protected $id;

    /**
     * Data of this form
     * @var mixed
     */
    protected $data;

    /**
     * Assigned rows
     * @var array
     */
    protected $rows;

    /**
     * Validation errors
     * @var array
     */
    protected $validationErrors;

    /**
     * Gets the id of this form, can also be used for suffixes for field names
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the data from this form
     * @return mixed Data of this form
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Checks if a row is set to the form
     * @param string $name Name of the row
     * @return boolean
     */
    public function hasRow($name) {
        return isset($this->rows[$name]);
    }

    /**
     * Gets a row from the form
     * @param string $name Name of the row
     * @return \ride\library\form\row\Row
     * @throws \ride\library\form\exception\FormException
     */
    public function getRow($name) {
        if (!isset($this->rows[$name])) {
            throw new FormException('Could not get row with name "' . $name . '": row not set');
        }

        return $this->rows[$name];
    }

    /**
     * Gets the rows
     * @return array
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Gets the validation errors for the provided field
     * @return array
     */
    public function getValidationErrors($field = null) {
        if ($field === null) {
            return $this->validationErrors;
        }

        $messages = array();

        if (isset($this->validationErrors[$field])) {
            foreach ($this->validationErrors[$field] as $error) {
                $messages[(string) $error] = true;
            }
        }

        return $messages;
    }

}