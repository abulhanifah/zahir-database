<?php 
namespace ZahirDB\Firebird\Query\Grammars;

use Firebird\Query\Grammars\FirebirdGrammar as BaseGrammar;

class FirebirdGrammar extends BaseGrammar {
  /**
   * Compile an lastInsertId select clause.
   *
   * @param  string $seq
   * @param  string $table
   * @param  string $field
   * @return string
   */
  public function compileLastInsertId($seq=false,$table,$field)
  {
    if ($seq) {
      return 'SELECT NEXT VALUE FOR ' . $seq . ' FROM RDB$DATABASE';
    } else {
      return 'SELECT (MAX('.$field.') + 1) as GEN_ID FROM '.$table;
    }
  }
}
