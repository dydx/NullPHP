<?php

/**
 * Sample Usage:
 *
 *	class Page extends Controller
 *	{
 *		function applyInputToModel()
 *		{
 *			$mto = new MTO('page');
 *			$mto->setModelValue('title', 'demo site);
 *			$mto->setModelValue('body', 'this is just some sample text');
 *	 		return $mto;
 *		}
 *	}
 *	Page::sendResponse(new Page());
 */

interface IController
{
	function setMto(IModelXfer $mto);
	static function sendResponse(IController $controller);
	function applyInputToModel();
}

abstract class Controller implements IController
{
	protected $mto;
	
	function setMto(IModelXfer $mto)
	{
		$this->mto = $mto;
	}
		
	static function sendResponse(IController $controller)
	{
		$controller->setMto($controller->applyInputToModel());
		$controller->mto->applyModelToView();
	}
}

interface IModelXfer
{
	function setView($view);
	function setModel($model);
	function setModelValue($key, $value);
	function applyModelToView();
}

abstract class AbstractMTO implements IModelXfer
{
	protected $view;
	protected $model;
	
	function setView($view)
	{
		$this->view = strtolower($view);
	}
	
	function setModel($model)
	{
		$this->model = strtolower($model);
	}
	
	function setModelValue($key, $value)
	{
		$this->model[$key] = $value;
	}
	
	protected function preserveSession()
	{
		$session = $GLOBALS['_SESSION'];
		unset($GLOBALS);
		$GLOBALS['_SESSION'] = $session;
	}
}

class MTO extends AbstractMTO
{
	function __construct($view)
	{
		$this->setView($view);
	}
	
	function applyModelToView()
	{
		$this->preserveSession();
		$model = $this->model;
		// this hack allows you to generally
		// call the View without need to 
		// specify a path.
		//
		// $mto = new MTO('page');
		require_once(BASEPATH'views/'.$this->view.EXT);
	}
}

?>
