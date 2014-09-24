<?php

namespace ride\library\form\component;

/**
 * A component with support for a web/HTML frontend
 */
interface HtmlComponent extends Component {

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
