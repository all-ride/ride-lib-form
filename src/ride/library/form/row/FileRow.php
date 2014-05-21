<?php

namespace ride\library\form\row;

use ride\library\form\exception\FormException;
use ride\library\validation\factory\ValidationFactory;
use ride\library\system\file\FileSystem;
use ride\library\system\file\File;
use ride\library\StringHelper;

/**
 * File row
 */
class FileRow extends AbstractRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'file';

    /**
     * Option for the upload path
     * @var string
     */
    const OPTION_PATH = 'path';

    /**
     * Option to overwrite existing files
     * @var string
     */
    const OPTION_OVERWRITE = 'overwrite';

    /**
     * Instance of the file system
     * @var \ride\library\system\file\FileSystem
     */
    protected $fileSystem;

    /**
     * Default upload path
     * @var string|\ride\library\system\file\File
     */
    protected $defaultUploadPath;

    /**
     * Absolute paths which should be made relative
     * @var array
     */
    protected $absolutePaths = array();

    /**
     * Sets the instance of the file system
     * @param \ride\library\system\file\FileSystem $fileSystem
     * @return null
     */
    public function setFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Sets the default upload path
     * @param string $defaultUploadPath
     * @return null
     */
    public function setDefaultUploadPath($defaultUploadPath) {
        $this->defaultUploadPath = $defaultUploadPath;
    }

    /**
     * Processes the attributes before creating the widget
     * @param array $attributes Attributes by reference
     * @return null
     */
    protected function processAttributes(array &$attributes) {
        $validators = $this->getOption(self::OPTION_VALIDATORS);
        if (!$validators || $this->data) {
            return;
        }

        foreach ($validators as $name => $validator) {
            if ($name == 'required' || $validator instanceof RequiredValidator) {
                $attributes['required'] = 'required';

                break;
            }
        }
    }

    /**
     * Adds a absolute path
     * @param string|\ride\library\system\file\File $path
     * @return null
     */
    public function addAbsolutePath($path) {
        if ($path instanceof File) {
            $path = $path->getAbsolutePath();
        }

        $this->absolutePaths[$path] = true;
    }

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        if (!isset($values[$this->name])) {
            return;
        }

        $files = $values[$this->name];
        if (!is_array($files)) {
            $this->data = $files;

            return;
        }

        $oldData = $this->data;

        $path = $this->getOption(self::OPTION_PATH, $this->defaultUploadPath);
        if (!$path) {
            throw new FormException('Could not process ' . $this->name . ': no upload path provided, use the "' . self::OPTION_PATH . '" option.');
        } elseif (!$path instanceof File) {
            $path = $this->fileSystem->getFile($path);
        }

        $path->create();
        if (!$path->isWritable()) {
            throw new FormException('Upload path ' . $path->getAbsolutePath() . ' is not writable');
        }

        $overwrite = $this->getOption(self::OPTION_OVERWRITE);
        $isMultiple = $this->getOption(self::OPTION_MULTIPLE);

        if (!$isMultiple) {
            $files = array($files);
        } else {
            $this->data = array();
        }

        foreach ($files as $index => $file) {
            if (!isset($file['error'])) {
                // not a file array, take data wichout processing
                if ($isMultiple) {
                    $this->data[$index] = $file;
                } else {
                    $this->data = $file;
                }

                continue;
            } elseif ($file['error'] == UPLOAD_ERR_NO_FILE) {
                // empty field
                if ($isMultiple) {
                    if (isset($oldData[$index])) {
                        $this->data[$index] = $oldData[$index];

                        unset($oldData[$index]);
                    }
                } else {
                    $this->data = $oldData;

                    $oldData = null;
                }

                continue;
            }

            $this->isUploadError($file);

            // prepare file name
            $uploadFileName = StringHelper::safeString($file['name'], '_', false);

            $uploadFile = $path->getChild($uploadFileName);
            if (!$overwrite) {
                $uploadFile = $uploadFile->getCopyFile();
            }

            // move file from temp to upload path
            if (!move_uploaded_file($file['tmp_name'], $uploadFile->getPath())) {
                throw new FormException('Could not move the uploaded file ' . $file['tmp_name'] . ' to ' . $uploadFile->getPath());
            }
            $uploadFile->setPermissions(0644);

            // make path relative
            $uploadPath = $uploadFile->getAbsolutePath();
            foreach ($this->absolutePaths as $absolutePath => $null) {
                if (strpos($uploadPath, $absolutePath) === 0) {
                    $uploadPath = str_replace($absolutePath . '/', '', $uploadPath);

                    break;
                }
            }

            // set to data
            if ($isMultiple) {
                $this->data[$index] = $uploadPath;
            } else {
                $this->data = $uploadPath;
            }
        }
    }

    /**
     * Checks whether a file upload error occured
     * @return null
     * @throws \ride\library\form\exception\FormException when an upload error
     * occured
     */
    protected function isUploadError($file) {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                return;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the maximum upload size';

                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file was only partially uploaded';

                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'No temporary directory to upload the file to';

                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk';

                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'The upload was stopped by a PHP extension';

                break;
            default:
                $message = 'The upload was stopped by an unknown error';

                break;
        }

        throw new FormException('Could not upload ' . $file['name'] . ': ' . $message);
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $path = $this->getOption(self::OPTION_PATH, $this->defaultUploadPath);
        if (!$path) {
            throw new FormException('Could not build ' . $this->name . ': no upload path provided, use the "' . self::OPTION_PATH . '" option.');
        }

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);
    }

}
