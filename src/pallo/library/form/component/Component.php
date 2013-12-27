<?php

namespace pallo\library\form\component;

use pallo\library\form\FormBuilder;

/**
 * Interface for a form component, a collection/definition of fields
 */
interface Component {

    /**
     * Gets the name of this component, used when this component is the root
     * of the form to be build
     * @return string
     */
    public function getName();

    /**
     * Gets the data type for the data of this form component
     * @return string|null A string for a data class, null for an array
     */
    public function getDataType();

    /**
     * Parse the data to form values for the component rows
     * @param mixed $data
     * @return array $data
     */
    public function parseSetData($data);

    /**
     * Parse the form values to data of the component
     * @param array $data
     * @return mixed $data
     */
    public function parseGetData(array $data);

    /**
     * Prepares the form by adding field definitions
     * @param pallo\library\form\FormBuilder $builder
     * @param array $options
     * @return null
     */
    public function prepareForm(FormBuilder $builder, array $options);

}