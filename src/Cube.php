<?php
namespace Robo\RoboDB;

use JsonSerializable;

class Cube implements JsonSerializable {
    protected $data = [];
    protected $cols = [];
    protected $table = '';

    protected function to_arr($data) {
        if(is_array($data)) return $data;
        return json_decode(json_encode($data), true);
    }

    function __construct($table='', $cols=[], $data=[]) {
        $this->table = $table;
        $this->cols = $this->to_arr($cols);
        $this->data = $this->to_arr($data);
    }

    function setData($data) {
        $this->data = $this->to_arr($data);
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

    function jsonSerialize() {
        return $this->data;
    }
}
