<?php

namespace ride\library\form;

use ride\library\validation\constraint\Constraint;

/**
 * Interface for a form builder
 */
interface FormBuilder {

    /**
     * Sets the id of this form, also used for suffixes for field ids
     * @param string $id Id of the form
     * @return null
     */
    public function setId($id);

    /**
     * Gets the id of this form, can also be used for suffixes for field names
     * @return string
     */
    public function getId();

    /**
     * Adds a row to the form
     * @param string $name Name of the row
     * @param string $type Type of the row
     * @param array $options Extra options for the row
     * @return \ride\library\form\row\Row
     */
    public function addRow($name, $type, array $options = array());

    /**
     * Removes a row from the form
     * @param string $name Name of the row
     * @return null
     */
    public function removeRow($name);

    /**
     * Checks if a row is available
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
     * Performs the build tasks
     * @return \ride\library\form\Form
     */
    public function build();

    /**
     * Adds an extra validation constraint
     * @param \ride\library\validation\constraint\Constraint $validationConstraint
     * @return null
     */
    public function addValidationConstraint(Constraint $validationConstraint);

    /**
     * Sets the extra validation constraint
     * @param \ride\library\validation\constraint\Constraint $validationConstraint
     * @return null
     */
    public function setValidationConstraint(Constraint $validationConstraint);

    /**
     * Gets the extra validation constraint
     * @return \ride\library\validation\constraint\Constraint
     */
    public function getValidationConstraint();

}
