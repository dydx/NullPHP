<?php

/**
 * This page is the access point to the rest
 * of the system. All that is needed for it
 * to behave is the load.inc.php file and
 * the getPage() function.
 *
 * getPage() can be passed through the URL,
 * and corresponds to a file in the Views
 * folder. Models, Controllers, and Views
 * are dynamically loaded according to what
 * is called via this function
 */

require_once('classes/load.inc.php');

/**
 * NullPHP's custom exception handler
 */
set_exception_handler('errors');

/**
 * getPage() is the backbone of how the MVC
 * calls content. For the purposes of this
 * demo, it has been set statically, but it
 * can be called dynamically via GET or POST
 */
Controller::getPage('demo');

?>
