<?php

namespace pallo\library\form\row;

use pallo\library\form\exception\FormException;
use pallo\library\form\row\factory\RowFactory;
use pallo\library\form\FormBuilder;
use pallo\library\reflection\ReflectionHelper;

abstract class AbstractFormBuilderRow extends AbstractRow implements FormBuilder {

    protected $reflectionHelper;

    protected $rowFactory;

    protected $rows;

    protected $isPrepared = false;

    /**
     * Sets an instance of the reflection helper
     * @param pallo\library\reflection\ReflectionHelper $reflectionHelper
     * @return null
     */
    public function setReflectionHelper(ReflectionHelper $reflectionHelper = null) {
        $this->reflectionHelper = $reflectionHelper;
    }

    /**
     * Sets the row factory to this row
     * @param pallo\library\form\row\factory\RowFactory $rowFactory
     * @return null
     */
    public function setRowFactory(RowFactory $rowFactory = null) {
        $this->rowFactory = $rowFactory;
    }

    /**
     * Sets the id of this form, also used for suffixes for field ids
     * @param string $id Id of the form
     * @return null
     */
    public function setId($id) {

    }

    /**
     * Gets the id of this form, can also be used for suffixes for field names
     * @return string
     */
    public function getId() {
        return $this->name;
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
     * Checks if a row is available
     * @param string $name Name of the row
     * @return boolean
     */
    public function hasRow($name) {
        return isset($this->rows[$name]);
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
     * Performs the build tasks
     * @return null
     */
    public function build() {

    }

    /**
     * Prepares the row for the form view
     * @return null
     */
    public function prepareForView() {
        $this->reflectionHelper = null;
        $this->rowFactory = null;

        if (!$this->rows) {
            return;
        }

        foreach ($this->rows as $row) {
            if (!$row instanceof self) {
                continue;
            }

            $row->setReflectionHelper(null);
            $row->setRowFactory(null);
        }
    }

}