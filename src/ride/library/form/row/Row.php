<?php

namespace ride\library\form\row;

use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;

/**
 * Row for a form
 */
interface Row {

    /**
     * Constructs a new form row
     * @param string $name Name of the row
     * @param array $options Extra options for the row or type implementation
     * @return null
     */
    public function __construct($name, array $options);

    /**
     * Gets the name of the type
     * @return string
     */
    public function getType();

    /**
     * Gets the name of this row
     * @return string
     */
    public function getName();

    /**
     * Gets the options
     * @return array
     */
    public function getOptions();

    /**
     * Gets an option from the row
     * @param string $option Name of the option
     * @param mixed $default Default value when the option is not set
     * @return mixed
     */
    public function getOption($option, $default = null);

    /**
     * Gets the label for this row
     * @return string
     */
    public function getLabel();

    /**
     * Gets the description for this row
     * @return string|null
     */
    public function getDescription();

    /**
     * Gets whether the field is disabled
     * @return boolean
     */
    public function isDisabled();

    /**
     * Gets whether the field is readonly
     * @return boolean
     */
    public function isReadOnly();

    /**
     * Gets whether the field is required
     * @return boolean
     */
    public function isRequired();

    /**
     * Gets the widget for this row, if applicable
     * @return \ride\library\form\widget\Widget
     */
    public function getWidget();

    /**
     * Sets whether this row has been rendered
     * @param boolean $isRendered
     * @return null
     */
    public function setIsRendered($isRendered);

    /**
     * Checks if this row has been rendered
     * @return boolean
     */
    public function isRendered();

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values);

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data);

    /**
     * Gets the data of this row
     * @return mixed
     */
    public function getData();

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory);

    /**
     * Applies the validation rules
     * @param \ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException);

    /**
     * Prepares the row for a serializable form view
     * @return null
     */
    public function prepareForView();

}
