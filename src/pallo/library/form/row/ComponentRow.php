<?php

namespace pallo\library\form\row;

use pallo\library\form\component\Component;
use pallo\library\form\exception\FormException;
use pallo\library\form\widget\GenericWidget;
use pallo\library\validation\exception\ValidationException;
use pallo\library\validation\factory\ValidationFactory;

/**
 * Row to nest a component
 */
class ComponentRow extends AbstractFormBuilderRow {

    /**
     * Type of this row
     * @var string
     */
    const TYPE = 'component';

    /**
     * Option for the component
     * @var string
     */
    const OPTION_COMPONENT = 'component';

    /**
     * Instance of the component
     * @var pallo\library\form\component\Component
     */
    protected $component;

    /**
     * Processes the request and updates the data of this row
     * @param array $values Submitted values
     * @return null
     */
    public function processData(array $values) {
        $this->initialize();

        if (isset($values[$this->name])) {
            if (is_array($values[$this->name])) {
                $values = $values[$this->name];
            } else {
                $values = array();
            }
        }

        foreach ($this->rows as $row) {
            try {
                $row->processData($values);
            } catch (ValidationException $exception) {
                $errors = $exception->getAllErrors();

                $exception = new ValidationException();
                foreach ($errors as $fieldName => $fieldErrors) {
                    $exception->addErrors($this->name . '[' . $fieldName . ']', $fieldErrors);
                }

                throw $exception;
            }
        }
    }

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        $this->initialize();

        if ($data !== null) {
            $class = $this->component->getDataType();

            if ($class) {
                if (!is_object($data) || get_class($data) != $class) {
                    if (is_object($data)) {
                        $type = get_class($data);
                    } else {
                        $type = gettype($data);
                    }

                    throw new FormException('Could not set data for ' . $this->name . ': instance of ' . $class . ' expected, recieved ' . $type);
                }
//             } elseif (!is_array($data)) {
//                 throw new FormException('Could not set data for ' . $this->name . ': data is not an array');
            }
        }

        $data = $this->component->parseSetData($data);

        $this->data = $data;

        foreach ($this->rows as $name => $row) {
            if ($this->data) {
                $row->setData($this->reflectionHelper->getProperty($this->data, $name));
            } else {
                $row->setData(null);
            }
        }
    }

    /**
     * Gets the data from this row
     * @return mixed Data of this form
     */
    public function getData() {
        $values = array();
        foreach ($this->rows as $name => $row) {
            $values[$name] = $row->getData();
        }

        return $this->component->parseGetData($values);
    }

    /**
     * Creates the data for this row
     * @param array $values Row values
     * @return mixed
     */
    protected function createData(array $values) {
        $class = $this->component->getDataType();

        if ($class) {
            return $this->reflectionHelper->createData($class, $values);
        }

        return $values;
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param pallo\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $this->initialize();

        $name = $this->getPropertyName($namePrefix);

        if (!$namePrefix) {
            $namePrefix = $this->getPropertyName($namePrefix) . '[';
        }

        if ($this->component->getDataType()) {
            $this->data = $this->getData();
            $data = $this->component->parseSetData($this->data);
        } else {
            $data = null;
        }

        foreach ($this->rows as $name => $row) {
            if ($data !== null) {
                $row->setData($this->reflectionHelper->getProperty($data, $name));
            }

            $row->buildRow($namePrefix, $idPrefix, $validationFactory);
        }

        if ($this->data !== null) {
            $default = $this->data;
        } else {
            $default = $this->getDefault();
        }

        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());
        $attributes['id'] = $name;

        $this->widget = new GenericWidget($this->type, $name, $default, $attributes);
    }

    /**
     * Applies the validation rules
     * @param pallo\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        foreach ($this->rows as $name => $row) {
            $row->applyValidation($validationException);
        }
    }

    /**
     * Initializes the row by nesting the form build process
     * @throws pallo\library\form\exception\FormException
     */
    protected function initialize() {
        if ($this->component) {
            return false;
        }

        $this->component = $this->getOption(self::OPTION_COMPONENT);
        if (!$this->component) {
            throw new FormException('Could not build ' . $this->name . ': no component option provided');
        } elseif (!$this->component instanceof Component) {
            throw new FormException('Could not build ' . $this->name . ': component is not an implementation of pallo\\library\\form\\component\\Component');
        }

        $options = $this->rowFactory->getBuildOptions();
        $options['data'] = $this->data;

        $this->component->prepareForm($this, $options);

        return true;
    }

}