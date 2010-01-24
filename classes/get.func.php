<?php
	
	function Load($model)
	{
		if(is_file('models/'.strtolower($model).'.model.php'))
			require_once('models/'.strtolower($model).'.model.php');
		else
			throw new Exception('Cannot load '.$model.' model');
	}

?>
