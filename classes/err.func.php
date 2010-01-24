<?php

	/**
	 * set_exception_handler("cust_exception_handler");
	 */
	function errors($e)
	{
		echo "<style type='text/css'>
				#error {
					border: 1px solid #333;
					color: #263e4a;
					font-family: 'Lucida Grande', veranda, arial, sans-serif;
					font-size: small;
					line-height: 1.5em;
					margin: 0;
					padding: 0;
					width: 60em;
				}
				
				#error h3 {
					background: #213635;
					color: #bed6b0;
					margin: 0;
					padding: .5em 1em;
				}
				
				#error p {
					margin: 0;
					padding: 1em;
				}
			</style>
			<div id='error'>
				<h3>Caught Exception</h3>
				<p>
					Error '{$e->getMessage()}' caught on line #{$e->getLine()} of {$e->getFile()}
				</p>
			</div>";

	}

?>
