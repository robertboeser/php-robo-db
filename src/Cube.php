<?php
namespace Robo\RoboDB;

use JsonSerializable;

class Cube implements JsonSerializable {
    protected $data = [];
    protected $cols = [];
    protected $table = '';
    protected $serializeTimezone = [];

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

    function setDateTime($name, $value) {
        // DateTime als UTC Timestamp speichern!
        if(!in_array($name, $this->cols)) return false;
        $dti = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        $this->data[$name] = $dti->format('Y-m-d\TH:i:s');
        return true;
    }

    function getDateTime($name) {
        // DateTime als UTC Timestamp speichern!
        if(!in_array($name, $this->cols)) return null;
        if(!isset($this->data[$name])) return null;
        // append timezone indicator
        return $this->data[$name] . 'Z';
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function __get($name) {
        return $this->get($name);
    }

    function jsonSerialize() {
        if(!$this->serializeTimezone) return $this->data;
        return $this->serializeDateTimeZone($this->serializeTimezone);
    }

    function setSerializeTimezone($names) {
        $this->serializeTimezone = $names;
    }

    function serializeDateTimeZone($names) {
        $copy = $this->getData();
        foreach($names as $name) {
            $key = array_search($name, $this->cols);
            if(false === $key) continue;
            if(!empty($copy[$key])) $copy[$key] .= 'Z';
        }
        return $copy;
    }
}
