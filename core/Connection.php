<?php

namespace Core;

class Connection
{
	private $driver;
	private $host;
	private $port;
	private $database;
	private $username;
	private $password;

	public function __construct()
	{
		$databaseConfig = require_once 'config/database.php';
		$this->driver = $databaseConfig['driver'];
		$this->host = $databaseConfig['host'];
		$this->port = $databaseConfig['port'];
		$this->database = $databaseConfig['database'];
		$this->username = $databaseConfig['username'];
		$this->password = $databaseConfig['password'];
	}

	public function getConnection()
	{
		if ($this->driver == 'pgsql') {
			$connection = pg_connect($this->getConnectionStringPgsql()) or die('the connection could not be established');
		}

		return $connection;
	}

	private function getConnectionStringPgsql()
	{
		return "host={$this->host} port={$this->port} dbname={$this->database} user={$this->username} password={$this->password}";
	}
}
