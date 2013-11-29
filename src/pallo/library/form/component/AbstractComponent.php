<?php

namespace pallo\library\form\component;

/**
 * Abstract implementation of a form component
 */
abstract class AbstractComponent implements Component {

    /**
     * Gets the name of this component, used when this component is the root
     * of the form to be build
     * @return string
     */
    public function getName() {
        return 'form';
    }

    /**
     * Gets the data type for the data of this form component
     * @return string|null A string for a data class, null for an array
     */
    public function getDataType() {
        return null;
    }

}