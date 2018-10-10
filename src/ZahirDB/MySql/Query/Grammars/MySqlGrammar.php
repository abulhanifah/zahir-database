<?php
namespace ZahirDB\MySql\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseGrammar;

class MySqlGrammar extends BaseGrammar
{
	/**
   * Compile an lastInsertId select clause.
   *
   * @param  string $seq
   * @param  string $table
   * @param  string $field
   * @return string
   */
  public function compileLastInsertId($seq,$table,$field)
  {
    return 'SELECT (MAX('.$field.') + 1) as GEN_ID FROM '.$table;
  }
}
