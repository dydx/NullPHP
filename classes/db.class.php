<?php

/**
 * Null DBW v6 (PHP5 Only)
 */
 
abstract class DB //abstract classes cannot be instantiated, must extend
{
	/**
	 * database connection vars
	 * edit these as needed
	 */
	const dbhost = ''; //database host, eg: localhost
	const dbuser = ''; //database user, eg: root
	const dbpass = ''; //database pass, eg: changeme
	const dbname = ''; //database name, eg: wp_db1
	
	/**
	 * global connection variables 
	 */
	private static $dblink = false;
	private static $dbconnected = false;

	/**
	 * class construct, must be extended and called by parent::
	 */
	public function __construct()
	{
		try {
			self::Connect();
		} catch(Exception $e) {
			die('Caught Exception: '.$e->getMessage());
		}
	}
	
	/**
	 * class destruct, do not call directly
	 */
	public function __destruct()
	{
		try {
			self::Disconnect();
		} catch(Exception $e) {
			die('Caught Exception: '.$e->getMessage());
		}
	}
	
	/**
	 * connect method, used for instantiating database connections
	 */
	private static function Connect()
	{
		if(!self::$dbconnected)
		{
			if((self::$dblink = @mysql_connect(self::dbhost, self::dbuser, self::dbpass)) === false)
				throw new Exception('Failed to connect to database server, incorrect connection variables.');
			if((@mysql_select_db(self::dbname, self::$dblink)) === false)
				throw new Exception('Failed to select database, unknown database name.');
			if((@mysql_query()) === false)
				throw new Exception('Impossible to use UTF-8 encoding with current database.');
			self::$dbconnected = true;
		}
	}
	
	/**
	 * disconnect method, destroys database connection
	 */
	private static function Disconnect()
	{
		if(self::$dbconnected === false)
			throw new Exception('No database connection present, cannot disconnect.');
		@mysql_close(self::$dblink);
	}
	
	/**
	 * extendable insert method
	 */
	protected function insert_bdd()
	{
		$table = eval('return '.get_class($this).'::$_table;');
		if(!$table)
			throw new Exception('Don\'t know what table to use for '.get_class($this));
		
		$fields = eval('return '.get_class($this).'::$_fields;');
		if(!$fields)
			throw new Exception('Don\'t know what fields describe '.get_class($this));
		$tb_fields = array_fill_keys($fields, 1);
		
		$txt_fields = '';
		$txt_values = '';
		$i = 0;
		
		foreach($this as $key => $value)
		{
			if(isset($tb_fields[$key]))
			{
				if($i > 0)
				{
					$txt_fields .= ',';
					$txt_values .= ',';
				}
				$txt_fields .= $key;
				
				$c = self::quote_smart($value);				
				if(is_numeric($value))
					$txt_values .= $c;
				elseif(is_null($value))
					$txt_values .= 'NULL';
				else
					$txt_values .= "'$c'";
				
				$i++;
			}
		}

		$sql = 'INSERT INTO '.$table.'('.$txt_fields.') VALUES ('.$txt_values.');'; 
		if(@mysql_query($sql, self::$dblink) === false)
			throw new Exception('Insertion error:<br />'.$sql.'<br />'.@mysql_error(self::$dblink));
		return @mysql_insert_id();
	}
	
	/**
	 * extendable update method
	 */
	protected function update_bdd()
	{
		$table = eval('return '.get_class($this).'::$_table;');
		if(!$table)
			throw new Exception('Don\'t know what table to use for '.get_class($this));
		
		$fields = eval('return '.get_class($this).'::$_fields;');
		if(!$fields)
			throw new Exception('Don\'t know what fields describe '.get_class($this));
		$tb_fields = array_fill_keys($fields, 1);
		
		$txt_query = 'UPDATE '.$table;
		$i = 0;
		
		foreach($this as $key => $value)
		{
			if(isset($tb_fields[$key]))
			{
				if($i == 0)
					$txt_query .= ' SET ';
				else
					$txt_query .= ',';
				
				$txt_value = self::quote_smart($value);
				if(is_null($value))
					$txt_query .= "$key = NULL";
				else
					$txt_query .= "$key = '$txt_value'";
				$i++;
			}
		}
		
		$i = 0;
		$primaryKey = eval('return '.get_class($this).'::$_primaryKey;');
		
		foreach($primaryKey as $key)
		{
			if($i == 0)
				$txt_query .= ' WHERE ';
			else
				$txt_query .= ' AND ';
			
			$c = self::quote_smart($this->$key); // __get()
			$txt_query .= "$key = '$c'";
		}
		
		$txt_query .= ';';
		if(@mysql_query($txt_query, self::$dblink) === false)
			throw new Exception('Update error:<br />'.$txt_query.'<br />'.@mysql_error(self::$dblink));
		if(@mysql_affected_rows(self::$dblink) == 0)
			throw new Exception('Update error:<br />'.$txt_query.'<br />0 records affected');
	}

	/**
	 * extendable delete method
	 */
	protected function delete_bdd()
	{	
		$table = eval('return '.get_class($this).'::$_table;');
		if(!$table)
			throw new Exception('Don\'t know what table to use for '.get_class($this));
		
		$primaryKey = eval('return '.get_class($this).'::$_primaryKey;');
		if(!$primaryKey)
			throw new Exception('Don\'t know what are primary key fields for '.get_class($this));
		
		$t = array();		
		foreach($primaryKey as $key => $value)
			$t[$value] = $this->$value;
		
		self::deleteDirectly($table, $primaryKey, $t);
	}
	
	/**
	 * initialize an object based on its primary key
	 */
	protected function init_by_primaryKey($Pk)
	{	
		$table = eval('return '.get_class($this).'::$_table;');
		if(!$table)
			throw new Exception('Don\'t know what table to use for '.get_class($this));
		
		$Pkfields = eval('return '.get_class($this).'::$_primaryKey;');
		if(!$Pkfields)
			throw new Exception('Don\'t know what are primary key fields for '.get_class($this));

		$Pkfields = array_flip($Pkfields);
		if(count(array_intersect_key($Pk, $Pkfields)) != count($Pkfields))
			throw new Exception('Primary key fields does not match those of table '.$table);
		
		$sql = 'SELECT * FROM '.$table;
		
		$i = 0;
		foreach($Pk as $key => $value)
		{
			if($i == 0)
				$sql .= ' WHERE ';
			else
				$sql .= ' AND ';
			
			$c = self::quote_smart($value);
			$sql .= "$key = '$c'";
		}
		
		$result = @mysql_query($sql);
		if(!$result)
			throw new Exception('Invalid request to init by primary key');
		if($d = @mysql_fetch_object($result))
		{
			foreach(get_object_vars($d) as $var => $value)
				$this->$var = $value;
		}
		else
			throw new Exception('No record for this primary key');
	}
	
	/**
	 * retrieve primary key from database
	 */
	protected function getPrimaryKey()
	{
		if(!self::$dbconnected)
			self::connect();
		
		$table = eval('return '.get_class($this).'::$_table;');
		$keys = array();
		
		$result = @mysql_query('SHOW KEYS FROM '.$table, self::$dblink);
		if(!$result)
			throw new Exception('Impossible to get primary key(s) of table '.$table);		
		while($row = @mysql_fetch_assoc($result))
		{
			if ($row['Key_name'] == 'PRIMARY')
				$keys[$row['Seq_in_index'] - 1] = $row['Column_name'];
		}
		
		return $keys;
	}
	
	/**
	 * retrieve fieldnames from database
	 */
	protected function getFields()
	{
		if(!self::$dbconnected)
			self::connect();
		
		$table = eval('return '.get_class($this).'::$_table;');
		$tb = array();
		
		$result = @mysql_query('SHOW COLUMNS FROM '.$table, self::$dblink);
		if(!$result)
			throw new Exception('Impossible to get information about table '.$table);		
		while($row = @mysql_fetch_assoc($result))
			$tb[] = $row['Field'];
		
		return $tb;
	}	
	
	/**
	 * directly delete an entry
	 */
	protected static function deleteDirectly($table, $Pkfields, $Pk)
	{
		if(!self::$dbconnected)
			self::connect();

		$Pkfields = array_flip($Pkfields);
		if(count(array_intersect_key($Pk, $Pkfields)) != count($Pkfields))
			throw new Exception('Primary key fields does not match those of table '.$table);
		
		$txt_query = 'DELETE FROM '.$table;
		$i = 0;
		
		foreach($Pk as $key => $value)
		{
			if($i == 0)
				$txt_query .= ' WHERE ';
			else
				$txt_query .= ' AND ';
			
			$c = self::quote_smart($value);
			$txt_query .= "$key='$c'";
		}
		
		$txt_query .= ';';
		if(@mysql_query($txt_query, self::$dblink) === false)
			throw new Exception('Delete error:<br />'.$txt_query.'<br />'.@mysql_error(self::$dblink));
		if(@mysql_affected_rows(self::$dblink) == 0) // Primary Key does not match any record => exception
			throw new Exception('Delete error:<br />'.$txt_query.'<br />0 records affected');
	}
	
	/**
	 * retrieve all rows from database
	 */
	protected static function getAll($class, $table)
	{
		if(!self::$dbconnected)
			self::connect();
		
		$etu = array();
	
		if(($result = @mysql_query('SELECT * FROM '.$table)) === false)
			throw new Exception('Impossible to retrieve all items of '.$table);
		while($d = @mysql_fetch_object($result))
		{
			$e = new $class;
			foreach(get_object_vars($d) as $var => $value)
				$e->$var = $value; // call __set
			$etu[] = $e;
		}
		
		return $etu;
	}
	
	/**
	 * $page = page::getWhere(array('PageID' => 125'));
	 */
	protected static function getWhere($class, $table, $where = array())
	{
		if(!self::$dbconnected)
			self::connect();
		$etu = array();
		//process WHERE statement
		$txt_query = 'SELECT * FROM '.$table;
		$i = 0;
		foreach($where as $key => $value)
		{
			if($i == 0)
				$txt_query .= ' WHERE ';
			else
				$txt_query .= ' AND ';
				
			$txt_value = self::quote_smart($value);
			if(is_numeric($value))
				$txt_query .= "$key = $value";
			elseif(is_null($value))
				$txt_query .= "$key = NULL";
			else
				$txt_query .= "$key = '$txt_value'";
			$i++;
		}
		
		$txt_query .= ';';
		if(($result = @mysql_query($txt_query)) === false)
			throw new Exception('Impossible to retrieve desired items of '.$table);
		while($d = @mysql_fetch_object($result))
		{
			$e = new $class;
			foreach(get_object_vars($d) as $var => $value)
				$e->$var = $value;
			$etu[] = $e;
		}
		return $etu;
	}
	
	/**
	 * retrieve row count from database
	 */
	protected static function getCount($table)
	{
		if(!self::$dbconnected)
			self::connect();
		
		$result = @mysql_query('SELECT COUNT(*) AS nb FROM '.$table);
		if(!$result)
			throw new Exception('Impossible to count items of '.$table);
		$row = @mysql_fetch_assoc($result);
		return $row['nb'];
	}
	
	/**
	 * quote database queries
	 */
	public static function quote_smart($value)
	{
		if(get_magic_quotes_gpc())
			$value = stripslashes($value);
		
		if(!is_numeric($value))
			$value = @mysql_real_escape_string($value);
		
		return $value;
	}
}
?>
