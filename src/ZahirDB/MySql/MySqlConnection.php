<?php 
namespace ZahirDB\MySql;

use Illuminate\Database\MySqlConnection as BaseConnection;
use ZahirDB\MySql\Query\Grammars\MySqlGrammar as QueryGrammar;
use ZahirDB\QueryBuilder;

class MySqlConnection extends BaseConnection {
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

	/**
    * Begin a fluent query against a database table.
    *
    * @param  string  $table
    * @return MySql\Query\Builder
    */
    public function table($table)
    {
        $processor = $this->getPostProcessor();

        $query = new QueryBuilder($this, $this->getQueryGrammar(), $processor);

        return $query->from($table);
    }
}
