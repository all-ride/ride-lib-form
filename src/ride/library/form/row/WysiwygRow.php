<?php

namespace ride\library\form\row;

use ride\library\validation\factory\ValidationFactory;

/**
 * Wysiwyg row
 */
class WysiwygRow extends TextRow implements HtmlRow {

    /**
     * Name of the row type
     * @var string
     */
    const TYPE = 'wysiwyg';

    /**
     * Base URL of the request
     * @var string
     */
    protected $baseUrl;

    /**
     * Code of the locale
     * @var string
     */
    protected $locale;

    /**
     * Properties for the wysiwyg implementation
     * @var array
     */
    protected $properties = array();

    /**
     * Sets the base URL
     * @param string $baseUrl Base URL of the request
     * @return null
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Sets the locale
     * @param string|mixed $locale Code of the locale
     * @return null
     */
    public function setLocale($locale) {
        $this->locale = (string) $locale;
    }

    /**
     * Sets the properties for the wysiwyg implementation
     * @param array $properties Key-value configuration pairs
     * @return null
     */
    public function setProperties(array $properties) {
        $this->properties = $properties;
    }

    /**
     * Performs necessairy build actions for this row
     * @param string $namePrefix Prefix for the row name
     * @param string $idPrefix Prefix for the field id
     * @param \ride\library\validation\factory\ValidationFactory $validationFactory
     * @return null
     */
    public function buildRow($namePrefix, $idPrefix, ValidationFactory $validationFactory) {
        $attributes = $this->getOption(self::OPTION_ATTRIBUTES, array());
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' wysiwyg';
        } else {
            $attributes['class'] = 'wysiwyg';
        }
        $this->setOption(self::OPTION_ATTRIBUTES, $attributes);

        parent::buildRow($namePrefix, $idPrefix, $validationFactory);

        $this->widget->setAttribute('required', null);
    }

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
