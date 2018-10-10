<?php 
namespace ZahirDB\Firebird\Schema;

use Illuminate\Database\Schema\Builder as BaseBuilder;

class Builder extends BaseBuilder {
	/**
     * Determine if the given table has a given column.
     *
     * @param  string  $table
     * @param  string  $column
     * @return bool
     */
    public function hasColumn($table, $column)
    {
        $columnListing = $this->getColumnListing($table);
        $temp = $columnListing;
        $columnListing = [];
        foreach ($temp as $key => $value) {
        	$columnListing[$key] = trim($value->FIELD_NAME);
        }
        return in_array(
            strtolower($column), array_map('strtolower', $columnListing)
        );
    }
}