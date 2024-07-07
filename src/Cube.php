<?php
namespace Robo\RoboDB;

use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;

/*
 * SET and GET from the Cube's perspective!
 */

class Cube implements JsonSerializable {
    protected $data = [];
    protected $info = [];
    protected $cols = [];
    protected $table = '';
    protected $dbTimezone = 'UTC';
    protected $apiTimezone = 'UTC';

    protected function to_arr($data) {
        if(is_array($data)) return array_merge([], $data);
        return json_decode(json_encode($data), true);
    }

    protected function initColsInfo($rows) {
        $this->cols = [];
        $this->info = [];
        if(!is_array($rows)) return false;
        foreach($rows as $r) {
            $name = reset($r);
            $this->cols[] = $name;
            $this->info[$name] = $r;
        }
        return true;
    }

    function __construct($table='', $info=[]) {
        $this->table = $table;
        $this->initColsInfo($info);
    }

    /*
     * generic I/O converter functions
     */

    protected function setData_datetime($value, $timezone) {
        if(!$value) return null;
        $tz = new DateTimeZone($timezone);
        $date = new DateTimeImmutable($value, $tz); // $tz is ignored is $value has one
        return $date->setTimeZone($tz);
    }

    protected function setData_number($value, $info) {
        if(!$value) {
            if('YES' === $info['Null']) return null;
            return 0;
        } else {
            return $value;
        }
    }

    protected function setData_string($value, $info) {
        if(!$value) {
            if('YES' === $info['Null']) return null;
            return '';
        } else {
            return $value;
        }
    }

    protected function getData_datetime($value, $format) {
        if(!$value) return null;
        return $value->format($format);
    }

    /*
     * setDataDB
     * is used by CubeDB, to get data from a SQL query and put it into the Cube
     */
    function setDataDB($data) {
        $data = $this->to_arr($data);
        if(!is_array($data)) return false;
        $this->data = [];
        foreach($data as $name => $value) {
            $info = @$this->info[$name];
            if(!$info) continue;
            switch($info['Type']) {
                case 'datetime':
                    $this->data[$name] = $this->setData_datetime($value, $this->dbTimezone);
                    break;
                case 'int':
                case 'decimal':
                case 'tinyint':
                    $this->data[$name] = $this->setData_number($value, $info);
                    break;
                default:
                    $this->data[$name] = $this->setData_string($value, $info);
            }
        }
        return true;
    }

    /*
     * getDataDB
     * is used by CubeDB, to get data from the Cube and use it in a SQL query
     */
    function getDataDB() {
        $copy = $this->to_arr($this->data);
        foreach($this->data as $name => $value) {
            $info = @$this->info[$name];
            if(!$info) continue;
            switch($info['Type']) {
                case 'datetime':
                    $copy[$name] = $this->getData_datetime($value, "Y-m-d H:i:s");
                    break;
            }
        }
        return $copy;
    }

    /*
     * setData
     * is used by the App, to put data into the Cube
     */
    function setData($data) {
        $data = $this->to_arr($data);
        if(!is_array($data)) return false;
        $this->data = [];
        foreach($data as $name => $value) {
            $this->set($name, $value);
        }
        return true;
    }

    /*
     * getData
     * is used by the App, to get data out of the Cube
     */
    function getData() {
        $copy = [];
        foreach($this->data as $name => $value) {
            $copy[$name] = $this->get($name);
        }
        return $copy;
    }

    function setTable($table) {
        $this->table = $table;
    }

    function getTable() {
        return $this->table;
    }

    function getInfo() {
        return json_decode(json_encode($this->info), true);
    }

    function set($name, $value) {
        $info = @$this->info[$name];
        if(!$info) return false;
        switch($info['Type']) {
            case 'datetime':
                $this->data[$name] = $this->setData_datetime($value, $this->apiTimezone);
                break;
            case 'int':
            case 'decimal':
            case 'tinyint':
                $this->data[$name] = $this->setData_number($value, $info);
                break;
            default:
                $this->data[$name] = $this->setData_string($value, $info);
        }
        return true;
    }

    function get($name) {
        if(!isset($this->data[$name])) return null;
        $info = @$this->info[$name];
        if(!$info) return null;
        $value = $this->data[$name];
        switch($info['Type']) {
            case 'datetime': return $this->getData_datetime($value, "Y-m-d H:i:sp");
            default: return $value;
        }
        return $value;
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function __get($name) {
        return $this->get($name);
    }

    function jsonSerialize(): mixed {
        return $this->getData();
    }
}
