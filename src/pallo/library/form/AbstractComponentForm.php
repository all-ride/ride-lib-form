<?php

namespace pallo\library\form;

use pallo\library\form\component\Component;
use pallo\library\reflection\ReflectionHelper;

/**
 * Abstract implementation of a form with component support
 */
abstract class AbstractComponentForm extends AbstractForm {

    /**
     * Component which defines this form
     * @var pallo\library\form\component\Component
     */
    protected $component;

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
     * @param pallo\library\form\component\Component $component
     * @return null
     */
    public function setComponent(Component $component) {
        $this->component = $component;
        $this->id = $component->getName();
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
     * Performs the build tasks
     * @return pallo\library\form\Form
     */
    public function build() {
        $this->options['data'] = $this->data;

        $this->rowFactory->setBuildOptions($this->options);

        if ($this->component) {
            $this->component->prepareForm($this, $this->options);
        }
    }

}