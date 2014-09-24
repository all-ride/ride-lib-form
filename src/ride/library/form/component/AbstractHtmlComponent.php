<?php

namespace ride\library\form\component;

/**
 * Abstract implementation of a form component
 */
abstract class AbstractHtmlComponent extends AbstractComponent implements HtmlComponent {

    /**
     * Gets all the javascript files which are needed for this row
     * @return array
     */
    public function getJavascripts() {
        return array();
    }

    /**
     * Gets all the inline javascripts which are needed for this row
     * @return array
    */
    public function getInlineJavascripts() {
        return array();
    }

    /**
     * Gets all the stylesheets which are needed for this row
     * @return array
     */
    public function getStyles() {
        return array();
    }

}
