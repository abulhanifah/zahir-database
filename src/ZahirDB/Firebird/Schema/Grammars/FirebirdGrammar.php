<?php 
namespace ZahirDB\Firebird\Schema\Grammars;

use Firebird\Schema\Grammars\FirebirdGrammar as BaseGrammar;
use Illuminate\Support\Fluent;
use Illuminate\Database\Schema\Blueprint;
use ZahirDB\Firebird\FirebirdConnection as Connection;

class FirebirdGrammar extends BaseGrammar {
  /**
   * Compile a create table command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @param  \Illuminate\Database\Connection  $connection
   * @return string
   */
  public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
  {
    $columns = implode(', ', $this->getColumns($blueprint));

    $sql = 'create table '.$this->wrapTable($blueprint)." ($columns)";

    return $sql;
  }

  /**
   * Compile the query to determine the list of columns.
   *
   * @return string
   */
  public function compileColumnListing($table)
  {
    return "SELECT LOWER(RDB\$FIELD_NAME) as FIELD_NAME FROM RDB\$RELATION_FIELDS WHERE RDB\$RELATION_NAME = '".$table."'";
  }

  /**
   * Compile an add column command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileAdd(Blueprint $blueprint, Fluent $command)
  {
    $columns = $this->prefixArray('add', $this->getColumns($blueprint));

    return 'alter table '.$this->wrapTable($blueprint).' '.implode(', ', $columns);
  }

  /**
   * Compile a drop table (if exists) command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileDropIfExists(Blueprint $blueprint, Fluent $command)
  {
    return 'drop table if exists '.$this->wrapTable($blueprint);
  }

  /**
   * Compile a drop column command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileDropColumn(Blueprint $blueprint, Fluent $command)
  {
    $columns = $this->prefixArray('drop', $this->wrapArray($command->columns));

    return 'alter table '.$this->wrapTable($blueprint).' '.implode(', ', $columns);
  }

  /**
   * Create the column definition for a char type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeChar(Fluent $column)
  {
    return "char({$column->length})";
  }

  /**
   * Create the column definition for a string type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeString(Fluent $column)
  {
    return "varchar({$column->length})";
  }

  /**
   * Create the column definition for a text type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeText(Fluent $column)
  {
    return 'blob sub_type text';
  }

  /**
   * Create the column definition for a medium text type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeMediumText(Fluent $column)
  {
    return 'blob sub_type text';
  }

  /**
   * Create the column definition for a long text type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeLongText(Fluent $column)
  {
    return 'blob sub_type text';
  }

  /**
   * Create the column definition for a big integer type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeBigInteger(Fluent $column)
  {
    return 'bigint';
  }

  /**
   * Create the column definition for an integer type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeInteger(Fluent $column)
  {
    return 'integer';
  }

  /**
   * Create the column definition for a medium integer type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeMediumInteger(Fluent $column)
  {
    return 'integer';
  }

  /**
   * Create the column definition for a tiny integer type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeTinyInteger(Fluent $column)
  {
    return 'integer';
  }

  /**
   * Create the column definition for a small integer type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeSmallInteger(Fluent $column)
  {
    return 'smallint';
  }

  /**
   * Create the column definition for a float type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeFloat(Fluent $column)
  {
    return 'float';
  }

  /**
   * Create the column definition for a double type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeDouble(Fluent $column)
  {
    return 'double precision';
  }

  /**
   * Create the column definition for a decimal type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeDecimal(Fluent $column)
  {
    return "decimal({$column->total}, {$column->places})";
  }

  /**
   * Create the column definition for a boolean type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeBoolean(Fluent $column)
  {
    return 'char(1)';
  }

  /**
   * Create the column definition for an enum type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeEnum(Fluent $column)
  {
    return 'varchar';
  }

  /**
   * Create the column definition for a json type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeJson(Fluent $column)
  {
    return 'blob sub_type text';
  }

  /**
   * Create the column definition for a jsonb type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeJsonb(Fluent $column)
  {
    return 'blob sub_type text';
  }

  /**
   * Create the column definition for a date type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeDate(Fluent $column)
  {
    return 'date';
  }

  /**
   * Create the column definition for a date-time type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeDateTime(Fluent $column)
  {
    return 'timestamp';
  }

  /**
   * Create the column definition for a date-time type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeDateTimeTz(Fluent $column)
  {
    return 'timestamp';
  }

  /**
   * Create the column definition for a time type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeTime(Fluent $column)
  {
    return 'time';
  }

  /**
   * Create the column definition for a time type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeTimeTz(Fluent $column)
  {
    return 'time';
  }

  /**
   * Create the column definition for a timestamp type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeTimestamp(Fluent $column)
  {
    if ($column->useCurrent) {
      return 'timestamp default CURRENT_TIMESTAMP';
    }

    return 'timestamp';
  }

  /**
   * Create the column definition for a timestamp type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeTimestampTz(Fluent $column)
  {
    if ($column->useCurrent) {
      return 'timestamp default CURRENT_TIMESTAMP';
    }

    return 'timestamp';
  }

  /**
   * Create the column definition for a binary type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeBinary(Fluent $column)
  {
    return 'blob';
  }

  /**
   * Create the column definition for a uuid type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeUuid(Fluent $column)
  {
    return 'char(36)';
  }

  /**
   * Create the column definition for an IP address type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeIpAddress(Fluent $column)
  {
    return 'varchar(45)';
  }

  /**
   * Create the column definition for a MAC address type.
   *
   * @param  \Illuminate\Support\Fluent  $column
   * @return string
   */
  protected function typeMacAddress(Fluent $column)
  {
    return 'varchar(17)';
  }

  /**
   * Compile a drop primary key command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileDropPrimary(Blueprint $blueprint, Fluent $command)
  {
    $index = $this->wrap($command->index);
    return 'alter table '.$this->wrapTable($blueprint).' drop constraint {$index}';
  }

  /**
   * Compile a drop unique key command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileDropUnique(Blueprint $blueprint, Fluent $command)
  {
      $index = $this->wrap($command->index);

      return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
  }

  /**
   * Compile a drop index command.
   *
   * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
   * @param  \Illuminate\Support\Fluent  $command
   * @return string
   */
  public function compileDropIndex(Blueprint $blueprint, Fluent $command)
  {
      $index = $this->wrap($command->index);

      return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
  }
}
