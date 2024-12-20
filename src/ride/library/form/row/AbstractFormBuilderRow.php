<?php

namespace ride\library\form\row;

use ride\library\form\exception\FormException;
use ride\library\form\row\factory\RowFactory;
use ride\library\form\FormBuilder;
use ride\library\reflection\ReflectionHelper;
use ride\library\validation\constraint\ChainConstraint;
use ride\library\validation\constraint\Constraint;
use ride\library\validation\constraint\GenericConstraint;

/**
 * Abstract implementation for a form row with form builder support
 */
abstract class AbstractFormBuilderRow extends AbstractRow implements FormBuilder, HtmlRow {

    /**
     * Instance of the reflection helper
     * @var \ride\library\reflection\ReflectionHelper
     */
    protected $reflectionHelper;

    /**
     * Instance of the row factory
     * @var \ride\library\form\row\factory\RowFactory
     */
    protected $rowFactory;

    /**
     * Subrows of this rows
     * @var array
     */
    protected $rows;

    /**
     * Flag to see if the this row has been prepared for usage
     * @var boolean
     */
    protected $isPrepared = false;

    /**
     * Extra validation constraint
     * @var \ride\library\validation\constraint\Constraint
     */
    protected $validationConstraint;

    /**
     * Sets an instance of the reflection helper
     * @param \ride\library\reflection\ReflectionHelper $reflectionHelper
     * @return null
     */
    public function setReflectionHelper(ReflectionHelper $reflectionHelper = null) {
        $this->reflectionHelper = $reflectionHelper;
    }

    /**
     * Sets the row factory to this row
     * @param \ride\library\form\row\factory\RowFactory $rowFactory
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
     * @return \ride\library\form\row\Row
     */
    public function addRow($name, $type, array $options = array()) {
        if (!$this->rowFactory) {
            throw new FormException('Could not add a row to the form: no RowFactory set');
        }

        $this->rows[$name] = $this->rowFactory->createRow($type, $name, $options);

        return $this->rows[$name];
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
     * @return \ride\library\form\Row
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
     * Adds an extra validation constraint
     * @param \ride\library\validation\constraint\Constraint $validationConstraint
     * @return null
     */
    public function addValidationConstraint(Constraint $validationConstraint) {
        if (!($this->validationConstraint instanceof ChainConstraint && !$this->validationConstraint instanceof GenericConstraint)) {
            $previousConstraint = $this->validationConstraint;

            $this->validationConstraint = new ChainConstraint();

            if ($previousConstraint) {
                $this->validationConstraint->addConstraint($previousConstraint);
            }
        }

        $this->validationConstraint->addConstraint($validationConstraint);
    }

    /**
     * Sets the extra validation constraint
     * @param \ride\library\validation\constraint\Constraint $validationConstraint
     * @return null
     */
    public function setValidationConstraint(Constraint $validationConstraint) {
        $this->validationConstraint = $validationConstraint;
    }

    /**
     * Gets the extra validation constraint
     * @return \ride\library\validation\constraint\Constraint|null
     */
    public function getValidationConstraint() {
        return $this->validationConstraint;
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
            $row->prepareForView();

            if (!$row instanceof self) {
                continue;
            }

            $row->setReflectionHelper(null);
            $row->setRowFactory(null);
        }
    }

    /**
     * Gets all the javascript files which are needed for this row
     * @return array|null
     */
    public function getJavascripts() {
        if (!$this->rows) {
            return array();
        }

        $javascripts = array();

        foreach ($this->rows as $name => $row) {
            if (!$row instanceof HtmlRow) {
                continue;
            }

            $javascripts = array_merge($javascripts, $row->getJavascripts());
        }

        return $javascripts;
    }

    /**
     * Gets all the inline javascripts which are needed for this row
     * @return array|null
    */
    public function getInlineJavascripts() {
        if (!$this->rows) {
            return array();
        }

        $inlineJavascripts = array();

        foreach ($this->rows as $name => $row) {
            if (!$row instanceof HtmlRow) {
                continue;
            }

            $inlineJavascripts = array_merge($inlineJavascripts, $row->getInlineJavascripts());
        }

        return $inlineJavascripts;
    }

    /**
     * Gets all the stylesheets which are needed for this row
     * @return array|null
     */
    public function getStyles() {
        if (!$this->rows) {
            return array();
        }

        $styles = array();

        foreach ($this->rows as $name => $row) {
            if (!$row instanceof HtmlRow) {
                continue;
            }

            $rowStyles = $row->getStyles();
            if ($rowStyles) {
                $styles = array_merge($styles, $rowStyles);
            }
        }

        return $styles;
    }

}
