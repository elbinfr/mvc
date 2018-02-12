<?php

namespace Core;

class BaseModel
{

    /**
     * The connection name for the model.
     */
    protected $connection;
    protected $currentConnection;

    /**
     * The table associated with the model.
     */
	protected $table;

    /**
     * The primary key for the model.
     */
    protected $primary;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    protected $autoIncrement;

	public function __construct()
	{
		$this->connection = new Connection();
		$this->currentConnection = $this->connection->getConnection();
	}

	public function getAll()
	{
		$result = $this->executeQuery("select * from $this->table order by id desc;");

		while ($row = pg_fetch_object($result)) {
			$resultSet[] = $row;
		}

		return $resultSet;
	}

	public function whereId($id)
	{
		$result = $this->executeQuery("select * from $this->table where id=$id");

		if ($row = pg_fetch_object($result)) {
			$resultSet = $row;
		}

		return $resultSet;
	}

	public function whereColumn($column, $value)
	{
		$queryString = "select * from $this->table where $column = $1 order by id desc";
		$queryName = 'selectByColumn';

        $result = $this->createAndExecutePrepareQuery($queryString, 'selectByColumn', [$value]);

		while ($row = pg_fetch_object($result)) {
			$resultSet[] = $row;
		}

		return $resultSet;
	}

    public function select($queryString, $values = array())
    {
        $resultSet = array();

        $result = $this->createAndExecutePrepareQuery($queryString, 'selectScript', $values);

        while ($row = pg_fetch_object($result)) {
            $resultSet[] = $row;
        }

        return $resultSet;
    }

    public function insert()
    {
        $attributes = $this->getAttributes('insert');

        $queryString = $this->generateInsertScript($attributes);

        $result = $this->createAndExecutePrepareQuery($queryString, 'insertScript', $this->getAttributesValues($attributes));

        return $result;
    }

    public function update()
    {
        $attributes = $this->getAttributes('update');

        $queryString = $this->generateUpdateScript($attributes);
        
        $result = $this->createAndExecutePrepareQuery($queryString, 'updateScript', $this->getAttributesValues($attributes));

        return $result;
    }

    public function delete()
    {
        $attributes = $this->getAttributes('update');

        $queryString = 'delete from ' . $this->table . ' where ' . $this->primary . '=$1';

        $result = $this->createAndExecutePrepareQuery($queryString, 'deleteScript', [
                    $attributes[$this->primary]
                ]);

        return $result;
    }

	private function executeQuery($queryString)
	{
		return pg_query(
					$this->currentConnection, 
					$queryString
				);
	}

    private function createAndExecutePrepareQuery($queryString, $queryName, $values = array())
    {
        if (!pg_prepare($this->currentConnection, $queryName, $queryString)) {
            die("Can't prepare $queryString : " . pg_last_error());
        }

        $result = pg_execute($this->currentConnection, $queryName, $values);

        return $result;
    }

    private function getAttributes($option)
    {
        foreach ($this as $key => $value) {
            $attributes[$key] = $value;
        }

        $baseAttributes = get_class_vars(__CLASS__);
        if ($option == 'insert') {
            if ($this->autoIncrement) {
                $baseAttributes[$this->primary] = "";
            }
        } elseif ($option == 'update') {
            $primaryValue = $attributes[$this->primary];
            unset($attributes[$this->primary]);
            $attributes[$this->primary] = $primaryValue;
        }

        return array_diff_key($attributes, $baseAttributes);
    }

    private function getAttributesValues($attributes = array())
    {
        foreach ($attributes as $key => $value) {
            $attributesValues[] = $value;
        }

        return $attributesValues;
    }

    private function generateInsertScript($attributes = array())
    {
        $queryColumns = "";
        $queryValues = "";

        $index = 1;
        foreach ($attributes as $key => $value) {
            $queryColumns = $queryColumns . $key . ',';
            $queryValues = $queryValues . '$' . $index . ',';
            $index++;
        }

        $queryColumns = substr($queryColumns, 0, strlen($queryColumns) -1 );
        $queryValues = substr($queryValues, 0, strlen($queryValues) -1 );
        $insertScript = "insert into $this->table($queryColumns) values($queryValues)";

        return $insertScript;
    }

    private function generateUpdateScript($attributes = array())
    {
        $queryColumns = '';
        $index = 1;
        foreach ($attributes as $key => $value) {
            if ($index == count($attributes)) {
                $queryColumns = substr($queryColumns, 0, strlen($queryColumns) -1 );
                $queryColumns = $queryColumns . ' where ' . $key . '=$' . $index; 
                break;
            }
            $queryColumns = $queryColumns . $key . '=$' . $index . ',';
            $index++;
        }

        $updateScript = "update $this->table set $queryColumns";

        return $updateScript;
    }
}
