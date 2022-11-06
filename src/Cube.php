<?php
namespace Robo\RoboDB;

class Cube {
    protected $data = [];
    protected $cols = [];
    protected $table = '';

    function __construct($table='', $cols=[], $data=[]) {
        $this->table = $table;
        $this->cols = $cols;
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

    function set($name, $value) {
        if(!in_array($name, $this->cols)) return false;
        $this->data[$name] = $value;
        return true;
    }

    function get($name) {
        if(!in_array($name, $this->cols)) return null;
        if(!isset($this->data[$name])) return null;
        return $this->data[$name];
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function __get($name) {
        return $this->get($name);
    }
}
