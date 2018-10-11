<?php 
namespace ZahirDB\Firebird;

use Firebird\Connection as BaseConnection;
use Kafoso\DoctrineFirebirdDriver\Driver\FirebirdInterbase\Driver as DoctrineDriver;
use ZahirDB\Firebird\Schema\Builder as SchemaBuilder;
use ZahirDB\Firebird\Schema\Grammars\FirebirdGrammar as SchemaGrammar;
use ZahirDB\Firebird\Query\Grammars\FirebirdGrammar as QueryGrammar;
use ZahirDB\QueryBuilder;

class FirebirdConnection extends BaseConnection {
	/**
	* Get the default query grammar instance
	*
	* @return Query\Grammars\FirebirdGrammar
	*/
	protected function getDefaultQueryGrammar()
	{
    	return new QueryGrammar;
  	}

  	/**
	* Get the default schema grammar instance.
	*
	* @return SchemaGrammar;
	*/
	protected function getDefaultSchemaGrammar() {
		return $this->withTablePrefix(new SchemaGrammar);
	}

  	/**
	* Get a schema builder instance for this connection.
	* @return Schema\Builder
	*/
	public function getSchemaBuilder()
	{
		if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); }

		return new SchemaBuilder($this);
	}

	/**
	* Get the default schema grammar instance.
	*
	* @return SchemaGrammar;
	*/
	protected function getDefaultSchemaGrammar() {
		return $this->withTablePrefix(new SchemaGrammar);
	}

	/**
	* Begin a fluent query against a database table.
	*
	* @param  string  $table
	* @return Firebird\Query\Builder
	*/
	public function table($table)
	{
		$processor = $this->getPostProcessor();

		$query = new QueryBuilder($this, $this->getQueryGrammar(), $processor);

		return $query->from($table);
	}

	/**
     * Get the Doctrine DBAL driver.
     *
     * @return Kafoso\DoctrineFirebirdDriver\Driver\FirebirdInterbase\Driver
     */
    protected function getDoctrineDriver()
    {
        return new DoctrineDriver;
    }
}
