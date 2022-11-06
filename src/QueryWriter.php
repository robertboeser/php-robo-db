<?php
namespace Robo\RoboDB;

class QueryWriter {
    protected $tbl;

    function setTable($table) {
        $this->tbl = $table;
    }

    function select($where = '') {
        if(!$where) return "SELECT * FROM {$this->tbl}";
        return "SELECT * FROM {$this->tbl} WHERE $where";
    }

    function insert($keys) {
        $vals = array_fill(0, count($keys), '?');
        $cols = implode(',', $keys);
        $vals = implode(',', $vals);
        return "INSERT INTO {$this->tbl} ($cols) VALUES ($vals)";
    }

    function update($keys) {
        $set = [];
        foreach($keys as $k) {
            if($k === 'id') continue;
            $set[] = "$k=?";
        }
        $set = implode(',', $set);

        return "UPDATE {$this->tbl} SET $set WHERE id = ?";
    }

    function delete() {
        return "DELETE FROM {$this->tbl} WHERE id = ?";
    }

    function tables() {
        return "SHOW TABLES";
    }

    function columns() {
        return "SHOW COLUMNS FROM {$this->tbl}";
    }

/*
    function getDataTypeString($datatype) {
        switch($datatype) {
            case static::DATATYPE_BOOL:     return 'TINYINT(1) UNSIGNED';
			case static::DATATYPE_UINT32:   return 'INT(11) UNSIGNED';
			case static::DATATYPE_DOUBLE:   return 'DOUBLE';
			case static::DATATYPE_TEXT_S:   return 'VARCHAR(191)';
			case static::DATATYPE_TEXT_M:	return 'VARCHAR(255)';
			case static::DATATYPE_TEXT_L:   return 'TEXT';
			case static::DATATYPE_TEXT_XL:  return 'LONGTEXT';
			case static::DATATYPE_MONEY:    return 'DECIMAL(10,2)';
            case static::DATATYPE_RBUID:    return 'CHAR(20)';
			case static::DATATYPE_DATE:     return 'DATE';
			case static::DATATYPE_DATETIME: return 'DATETIME';
			case static::DATATYPE_TIME:     return 'TIME';
            default: return '';
        }
    }
*/
}
