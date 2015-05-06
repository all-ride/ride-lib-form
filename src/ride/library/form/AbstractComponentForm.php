<?php

namespace ride\library\form;

use ride\library\form\component\Component;
use ride\library\form\exception\FormException;
use ride\library\form\row\ComponentRow;
use ride\library\reflection\ReflectionHelper;
use ride\library\validation\exception\ValidationException;

/**
 * Abstract implementation of a form with component support
 */
abstract class AbstractComponentForm extends AbstractForm {

    /**
     * Component which defines this form
     * @var \ride\library\form\component\Component
     */
    protected $component;

    /**
     * Component row for handling the component
     * @var \ride\library\form\row\ComponentRow
     */
    protected $componentRow;

    /**
     * Options for the preparation of the form
     * @var array
     */
    protected $options;

    /**
     * Constructs a new form
     * @return null
     */
    public function __construct(ReflectionHelper $reflectionHelper, array $options = array()) {
        parent::__construct($reflectionHelper);

        $this->component = null;
        $this->options = $options;
    }

    /**
     * Sets a form component to define this form
     * @param \ride\library\form\component\Component $component
     * @return null
     */
    public function setComponent(Component $component) {
        $this->component = $component;
        $this->id = $component->getName();
    }

    /**
     * Gets the data from this form
     * @return mixed Data of this form
     */
    public function getData() {
        if (!$this->componentRow) {
            return parent::getData();
        }

        if (!$this->dataNeedsProcessing) {
            return $this->data;
        }

        $this->data = $this->componentRow->getData();

        $this->dataNeedsProcessing = false;

        return $this->data;
    }

    /**
     * Creates the data for this form
     * @param array $values Row values
     * @return mixed
     */
    protected function createData(array $values) {
        if ($this->component) {
            $class = $this->component->getDataType();

            if ($class) {
                return $this->reflectionHelper->createData($class, $values);
            }
        }

        return $values;
    }

    /**
     * Prepares for the build task
     * @return \ride\library\form\Form
     */
    public function build() {
        $this->options['data'] = $this->data;

        $this->rowFactory->setBuildOptions($this->options);
    }

    /**
     * Build the rows
     * @param array $data Submitted data for the rows
     * @throws \ride\library\form\exception\FormException when no validation
     * factory set
     */
    protected function buildRows(array $data = null) {
        if (!$this->component) {
            parent::buildRows($data);

            return;
        }

        if (!$this->validationFactory) {
            throw new FormException('Could not build the form: no validation factory set');
        }

        $this->componentRow = new ComponentRow($this->component->getName(), array(
            'component' => $this->component,
        ));

        $this->componentRow->setReflectionHelper($this->reflectionHelper);
        $this->componentRow->setRowFactory($this->rowFactory);

        if ($this->data !== null) {
            $this->componentRow->setData($this->data);
        }

        if ($data !== null) {
            try {
                $this->componentRow->processData($data);
            } catch (ValidationException $exception) {
                $validationException = $this->getValidationException();

                $errors = $exception->getAllErrors();
                foreach ($errors as $fieldName => $fieldErrors) {
                    $validationException->addErrors($fieldName, $fieldErrors);
                }
            }

            $this->isSubmitted = true;
            $this->dataNeedsProcessing = true;
        } else {
            $this->isSubmitted = false;
        }

        $this->componentRow->buildRow('', '', $this->validationFactory);

        $actionRow = null;
        if (isset($this->rows[self::ROW_ACTION])) {
            $actionRow = $this->rows[self::ROW_ACTION];
        }

        $this->rows = $this->componentRow->getRows();
        $this->validationConstraint = $this->componentRow->getValidationConstraint();

        if ($actionRow) {
            $this->rows[self::ROW_ACTION] = $actionRow;
        }
    }

}
