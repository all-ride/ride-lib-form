<?php

namespace ride\library\form\view;

/**
 * Interface for a form view
 */
interface View {

    /**
     * Gets the id of this form, can also be used for suffixes for field names
     * @return string
     */
    public function getId();

    /**
     * Gets the data from this form
     * @return mixed Data of this form
     */
    public function getData();

    /**
     * Checks if a row is set to the form
     * @param string $name Name of the row
     * @return boolean
     */
    public function hasRow($name);

    /**
     * Gets a row from the form
     * @param string $name Name of the row
     * @return \ride\library\form\row\Row
     */
    public function getRow($name);

    /**
     * Gets the rows
     * @return array
     */
    public function getRows();

    /**
     * Gets the validation error for the provided field
     * @param $field
     * @return array
     */
    public function getValidationErrors($field = null);

}