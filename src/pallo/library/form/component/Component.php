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
     * Prepares the form by adding field definitions
     * @param pallo\library\form\FormBuilder $builder
     * @param array $options
     * @return null
     */
    public function prepareForm(FormBuilder $builder, array $options);

}