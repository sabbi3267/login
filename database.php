<?php

class Database {

	private static $dbName = 'nalam355wi19';
	private static $dbHost = '10.8.30.49' ;
	private static $dbUsername = 'nalam355wi19';
	private static $dbUserPassword = 'nalamdbwi19n';
	
	private static $cont  = null;
	
	public function __construct() {
		exit('Init function is not allowed');
	}
	
	/*
     * This method connects to the databse 
     * - Input: infromation for the data base
     * - Processing: php
     * - Output: establishes connection with the database
     * - Precondition:  existing database 
     * - Postcondition: connects to the database or dies
     */
	public static function connect()
	{
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);  
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
        }
       } 
       return self::$cont;
	}
	
	public static function disconnect()
	{
		self::$cont = null;
	}
}
?>
