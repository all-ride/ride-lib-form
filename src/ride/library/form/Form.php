<?php

namespace ride\library\form;

use ride\library\validation\exception\ValidationException;

/**
 * Interface for a form
 */
interface Form extends FormBuilder {

    /**
     * Sets the data to this form
     * @param mixed $data Data for this form
     * @return null
     */
    public function setData($data);

    /**
     * Gets the data from this form
     * @return mixed Data of this form
     */
    public function getData();

    /**
     * Validates this form
     * @return null
     * @throws \ride\library\validation\exception\ValidationException when the
     * data in the form could not be validated
     */
    public function validate();

    /**
     * Sets the validation exception
     * @param \ride\library\validation\exception\ValidationException $exception
     * @return null
     */
    public function setValidationException(ValidationException $exception);

    /**
     * Gets the validation exception of the last validate call
     * @return \ride\library\validation\exception\ValidationException|null
     */
    public function getValidationException();

    /**
     * Checks if this form is submitted
     * @return boolean
     */
    public function isSubmitted();

    /**
     * Gets a viewable and serializable version of this form
     * @return \ride\library\form\view\View
     */
    public function getView();

}
