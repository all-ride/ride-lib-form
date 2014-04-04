<?php

namespace ride\library\form\row;

/**
 * A row with support for a web/HTML frontend
 */
interface HtmlRow extends Row {

     /**
      * Gets all the javascript files which are needed for this row
      * @return array
      */
     public function getJavascripts();

     /**
      * Gets all the inline javascripts which are needed for this row
      * @return array
     */
     public function getInlineJavascripts();

     /**
      * Gets all the stylesheets which are needed for this row
      * @return array
      */
     public function getStyles();

}
