<?php

namespace pallo\library\form;

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
     * @return null
     */
    public function addRow($name, $type, array $options = array());

    /**
     * Removes a row from the form
     * @param string $name Name of the row
     * @return null
     */
    public function removeRow($name);

    /**
     * Gets a row from the form
     * @param string $name Name of the row
     * @return pallo\library\html\form\row\Row
     */
    public function getRow($name);

    /**
     * Gets the rows
     * @return array
     */
    public function getRows();

    /**
     * Performs the build tasks
     * @return pallo\library\form\Form
     */
    public function build();

}