<?php

namespace ride\library\form\view;

/**
 * Generic implementation of a form view
 */
class GenericView extends AbstractView {

    /**
     * Constructs a generic form view
     * @param string $id Id of the form
     * @param mixed $data Data of the form
     * @param array $rows Rows of the form
     * @param array $validationErrors
     * @return null
     */
    public function __construct($id, $data = null, array $rows = array(), array $validationErrors = array()) {
        $this->id = $id;
        $this->data = $data;
        $this->rows = $rows;
        $this->validationErrors = $validationErrors;
    }

}