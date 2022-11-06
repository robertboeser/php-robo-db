<?php
namespace Robo\RoboCube;

class CubeEntity {
    protected $data = [];
    protected $cols = [];
    protected $table = '';

    function __construct($table='', $cols=[], $data=[]) {
        $this->table = $table;
        $this->data = $data;
    }

    function setData($data) {
        $this->data = $data;
    }

    function getData() {
        return array_merge([], $this->data);
    }

    function setTable($table) {
        $this->table = $table;
    }

    function getTable() {
        return $this->table;
    }

    function __set($name, $value) {
        if(!in_array($name, $this->cols)) return false;
        $this->data[$name] = $value;
        return true;
    }

    function __get($name) {
        if(!in_array($name, $this->cols)) return null;
        if(!isset($this->data[$name])) return null;
        return $this->data[$name];
    }
}
