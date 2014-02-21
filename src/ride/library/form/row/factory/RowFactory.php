<?php

namespace ride\library\form\row\factory;

/**
 * Factory to create row types
 */
interface RowFactory {

    /**
     * Sets build options for the row factory
     * @param array $options
     * @return null
     */
    public function setBuildOptions(array $options);

    /**
     * Gets the build options
     * @return array:
     */
    public function getBuildOptions();

    /**
     * Creates a row
     * @param string $type Name of the row type
     * @param string $name Name of the row
     * @param array $options Extra options for the row
     * @return ride\library\form\row\Row
     */
    public function createRow($type, $name, $options);

}