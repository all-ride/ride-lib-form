<?php

namespace ride\library\form\row;

use ride\library\form\component\Component;
use ride\library\form\component\HtmlComponent;
use ride\library\form\exception\FormException;
use ride\library\form\widget\GenericWidget;
use ride\library\validation\exception\ValidationException;
use ride\library\validation\factory\ValidationFactory;

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
     * Option to embed the component
     * @var string
     */
    const OPTION_EMBED = 'embed';

    /**
     * Instance of the component
     * @var \ride\library\form\component\Component
     */
    protected $component;

    /**
     * Flag to see if this row has been initialized
     * @var boolean
     */
    protected $isInitialized;

    /**
     * Gets the component
     * @return \ride\library\form\component\Component
     * @throws \ride\library\form\exception\FormException when no or an invalid
     * component is set to this row
     */
    public function getComponent() {
        if ($this->component) {
            return $this->component;
        }

        $this->component = $this->getOption(self::OPTION_COMPONENT);
        if (!$this->component) {
            throw new FormException('Could not build ' . $this->name . ': no component option provided');
        } elseif (!$this->component instanceof Component) {
            throw new FormException('Could not build ' . $this->name . ': component is not an implementation of ride\\library\\form\\component\\Component');
        }

        return $this->component;
    }

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
            } else if (!$this->getOption(self::OPTION_EMBED)) {
                $values = array();
            }
        }

        $this->data = array();
        foreach ($this->rows as $rowName => $row) {
            try {
                $row->processData($values);
            } catch (ValidationException $exception) {
                $errors = $exception->getAllErrors();

                $exception = new ValidationException('Validation errors occured', 0, $exception);
                foreach ($errors as $fieldName => $fieldErrors) {
                    $positionOpen = strpos($fieldName, '[');
                    if ($positionOpen) {
                        $errorFieldName = $this->name . '[' . substr($fieldName, 0, $positionOpen) . ']' . substr($fieldName, $positionOpen);
                    } else {
                        $errorFieldName = $this->name . '[' . $fieldName . ']';
                    }

                    $exception->addErrors($errorFieldName, $fieldErrors);
                }

                throw $exception;
            }

            $this->data[$rowName] = $row->getData();
        }
    }

    /**
     * Sets the data to this row
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        $component = $this->getComponent();

        if ($data !== null) {
            $class = $component->getDataType();

            if ($class) {
                if (!is_object($data) || (get_class($data) != $class && !is_subclass_of($data, $class))) {
                    if (is_object($data)) {
                        $type = get_class($data);
                    } else {
                        $type = gettype($data);
                    }

                    throw new FormException('Could not set data for ' . $this->name . ': instance of ' . $class . ' expected, recieved ' . $type);
                }
            }
        }

        $this->data = $data;

        $this->initialize();

        $this->data = $component->parseSetData($this->data);

        foreach ($this->rows as $rowName => $row) {
            if ($this->data) {
                $row->setData($this->reflectionHelper->getProperty($this->data, $rowName));
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
        $this->initialize();

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
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $this->initialize();

        $name = $this->getPropertyName($namePrefix);
        if (!$namePrefix || !$this->getOption(self::OPTION_EMBED)) {
            $namePrefix = $this->getPropertyName($namePrefix) . '[';
        }

        if ($namePrefix == $this->getName() . '[') {
            // $namePrefix = '';
            $idPrefix = $this->getName() . '-';
        }

        if ($this->rows) {
            foreach ($this->rows as $rowName => $row) {
                if ($this->data !== null) {
                    $row->setData($this->reflectionHelper->getProperty($this->data, $rowName));
                }

                $row->buildRow($namePrefix, $idPrefix, $validationFactory);
            }
        }

        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());
        $attributes['id'] = $name;

        $this->widget = new GenericWidget($this->type, $name, null, $attributes);
    }

    /**
     * Applies the validation rules
     * @param \ride\library\validation\exception\ValidationException $validationException
     * @return null
     */
    public function applyValidation(ValidationException $validationException) {
        foreach ($this->rows as $name => $row) {
            $row->applyValidation($validationException);
        }

        if ($this->validationConstraint) {
            $this->validationConstraint->constrain($this->getData(), $validationException);
        }
    }

    /**
     * Initializes the row by nesting the form build process
     * @throws \ride\library\form\exception\FormException
     */
    protected function initialize() {
        if ($this->isInitialized) {
            return false;
        }

        $options = $this->rowFactory->getBuildOptions();
        $options['data'] = $this->data;

        $component = $this->getComponent();
        $component->prepareForm($this, $options);

        $this->isInitialized = true;

        return true;
    }

    /**
     * Gets all the javascript files which are needed for this row
     * @return array|null
     */
    public function getJavascripts() {
        $javascripts = parent::getJavascripts();

        $component = $this->getComponent();
        if ($component instanceof HtmlComponent) {
            $javascripts = array_merge($javascripts, $component->getJavascripts());
        }

        return $javascripts;
    }

    /**
     * Gets all the inline javascripts which are needed for this row
     * @return array|null
    */
    public function getInlineJavascripts() {
        $inlineJavascripts = parent::getInlineJavascripts();

        $component = $this->getComponent();
        if ($component instanceof HtmlComponent) {
            $inlineJavascripts = array_merge($inlineJavascripts, $component->getInlineJavascripts());
        }

        return $inlineJavascripts;
    }

    /**
     * Gets all the stylesheets which are needed for this row
     * @return array|null
     */
    public function getStyles() {
        $styles = parent::getStyles();

        $component = $this->getComponent();
        if ($component instanceof HtmlComponent) {
            $styles = array_merge($styles, $component->getStyles());
        }

        return $styles;
    }

}
