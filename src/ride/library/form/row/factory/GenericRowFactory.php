<?php

namespace ride\library\form\row\factory;

use ride\library\form\exception\FormException;
use ride\library\form\row\ButtonRow;
use ride\library\form\row\CollectionRow;
use ride\library\form\row\ComponentRow;
use ride\library\form\row\DateRow;
use ride\library\form\row\EmailRow;
use ride\library\form\row\FileRow;
use ride\library\form\row\HiddenRow;
use ride\library\form\row\ImageRow;
use ride\library\form\row\LabelRow;
use ride\library\form\row\NumberRow;
use ride\library\form\row\OptionRow;
use ride\library\form\row\PasswordRow;
use ride\library\form\row\SelectRow;
use ride\library\form\row\StringRow;
use ride\library\form\row\TextRow;
use ride\library\form\row\WebsiteRow;
use ride\library\form\row\WysiwygRow;
use ride\library\reflection\ReflectionHelper;
use ride\library\system\file\FileSystem;

/**
 * Factory to create row types
 */
class GenericRowFactory extends AbstractRowFactory {

    /**
     * Instance of the reflection helper
     * @var \ride\library\reflection\ReflectionHelper
     */
    protected $reflectionHelper;

    /**
     * Instance of the file system
     * @var \ride\library\system\file\FileSystem
     */
    protected $fileSystem;

    /**
     * Sets an instance of the reflection helper
     * @param \ride\library\reflection\ReflectionHelper $reflectionHelper
     * @return null
     */
    public function setReflectionHelper(ReflectionHelper $reflectionHelper) {
        $this->reflectionHelper = $reflectionHelper;
    }

    /**
     * Sets the instance of the file system
     * @param \ride\library\system\file\FileSystem $fileSystem
     * @return null
    */
    public function setFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Creates a row
     * @param string $type Name of the row type
     * @param string $name Name of the row
     * @param array $options Extra options for the row
     * @return \ride\library\form\row\Row
     */
    public function createRow($type, $name, $options) {
        switch ($type) {
            case 'collection':
                 $row = new CollectionRow($name, $options);
                 $row->setReflectionHelper($this->reflectionHelper);
                 $row->setRowFactory($this);

                 break;
            case 'component':
                 $row = new ComponentRow($name, $options);
                 $row->setReflectionHelper($this->reflectionHelper);
                 $row->setRowFactory($this);

                 break;
            case 'boolean':
            case 'option':
                $row = new OptionRow($name, $options);

                break;
            case 'select':
                $row = new SelectRow($name, $options);

                break;
            case 'date':
            case 'datetime':
                $row = new DateRow($name, $options);

                break;
            case 'integer':
            case 'number':
                $row = new NumberRow($name, $options);

                break;
            case 'string':
                $row = new StringRow($name, $options);

                break;
            case 'binary':
            case 'text':
            case 'serialize':
                $row = new TextRow($name, $options);

                break;
            case 'wysiwyg':
                $row = new WysiwygRow($name, $options);

                break;
            case 'file':
                $row = new FileRow($name, $options);

                break;
            case 'image':
                $row = new ImageRow($name, $options);

                break;
            case 'email':
                $row = new EmailRow($name, $options);

                break;
            case 'website':
                $row = new WebsiteRow($name, $options);

                break;
            case 'password':
                $row = new PasswordRow($name, $options);

                break;
            case 'hidden':
                $row = new HiddenRow($name, $options);

                break;
            case 'label':
                $row = new LabelRow($name, $options);

                break;
            case 'button':
                $row = new ButtonRow($name, $options);

                break;
            default:
                throw new FormException('Could not create row for ' . $name . ': no implementation available for ' . $type);

                break;
        }

        if ($row instanceof FileRow) {
            $row->setFileSystem($this->fileSystem);

            foreach ($this->absolutePaths as $absolutePath => $null) {
                $row->addAbsolutePath($absolutePath);
            }
        }

        return $row;
    }

}
