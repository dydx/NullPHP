<?php

/**
 * This code is the default controller that
 * that ships with NullPHP, it is meant as a
 * demonstration of what you can do with.
 * 
 * odds are that you will have no use for
 * this file on a production site, so delete
 * it as you see fit.
 */

class Demo extends Controller //extend base controller
{
	function applyInputToModel() //implement parent function
	{
		//instantiate model-tranfer-object (view)
		$mto = new MTO('demo');  //there is no need to specify a path
		$mto->setModelValue('title', 'Demo'); //set the contents of the data array
		$mto->setModelValue('content', 'This page is a demo of how this system works');
		return $mto; //return model transfer object
	}
}

Demo::sendResponse(new Demo());

?>
