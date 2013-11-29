<?php

namespace pallo\library\form;

use pallo\library\validation\exception\ValidationException;

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
     * Sets the validation exception
     * @param pallo\library\validation\exception\ValidationException $exception
     * @return null
     */
    public function setValidationException(ValidationException $exception);

    /**
     * Gets the validation exception of the last validate call
     * @return pallo\library\validation\exception\ValidationException|null
     */
    public function getValidationException();

    /**
     * Checks if this form is submitted
     * @return boolean
     */
    public function isSubmitted();

    /**
     * Gets a viewable and serializable version of this form
     * @return pallo\library\form\view\View
     */
    public function getView();

}