<?php

namespace Core;

class Model extends BaseModel
{
	protected $table;

	public function __construct($table)
	{
		$this->table = $table;
		//parent::__construct($table);
	}
}
