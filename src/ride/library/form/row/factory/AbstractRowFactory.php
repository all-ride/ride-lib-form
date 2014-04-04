<?php

namespace ride\library\form\row\factory;

use ride\library\system\file\File;

/**
 * Factory to create row types
 */
abstract class AbstractRowFactory implements RowFactory {

    /**
     * Build options
     * @var array
     */
    protected $options = array();

    /**
     * Absolute paths to make file uploads relative
     * @var array
     */
    protected $absolutePaths = array();

    /**
     * Sets build options
     * @param array $options
     * @return null
     */
    public function setBuildOptions(array $options) {
        $this->options = $options;
    }

    /**
     * Gets the build options
     * @return array:
     */
    public function getBuildOptions() {
        return $this->options;
    }

    /**
     * Adds a absolute path to make file uploads relative
     * @param string|\ride\library\system\file\File $path
     * @return null
     */
    public function addAbsolutePath($path) {
        if ($path instanceof File) {
            $path = $path->getAbsolutePath();
        }

        $this->absolutePaths[$path] = true;
    }

}
