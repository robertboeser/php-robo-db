<?php
namespace Robo\RoboCube;

use Robo\RoboID\RoboB32 as RoboID;

class Repository {
    protected $adapter;
    protected $queryWriter;
    protected $idProvider;

    function findOne($table, $where='', $params=[]) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);
        $sql = $this->queryWriter->select("$where LIMIT 1");
        return $this->adapter->get($sql, $params);
    }

    function find($table, $where='', $params=[]) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);
        $sql = $this->queryWriter->select($where);
        return $this->adapter->get($sql, $params);
    }

    function add($table, $data) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);

        $data['id'] = $this->idProvider->genID();
        $keys = array_keys($data);
        $vals = array_values($data);
        $sql = $this->queryWriter->insert($keys);
        $res = $this->adapter->exec($sql, $vals);
        if(!$res) return false;
        return $data['id'];
    }

    function upd($table, $data) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);

        if(!$data['id']) return false;

        $id = $data['id'];
        unset($data['id']);
        $keys = array_keys($data);
        $vals = array_values($data);
        $vals[] = $id;

        $sql = $this->queryWriter->update($keys);
        $res = $this->adapter->exec($sql, $vals);
        if(!$res) return false;
        return $id;
    }

    function del($table, $id) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);
        $sql = $this->queryWriter->delete();
        $res = $this->adapter->exec($sql, [$id]);
        if(!$res) return false;
        return $id;
    }

    function fetchTableCols($table) {
        $this->checkPlausibleTable($table);
        $this->queryWriter->setTable($table);
        $sql = $this->queryWriter->columns();
        $res = $this->firstCol($sql);
        if(!$res) return [];
        return $res;
    }

    function firstCol($sql, $params=[]) {
        $res = $this->adapter->get($sql, $params);
        if(!is_array($res)) return false;
        $arr = [];
        foreach($res as $r) {
            $arr[] = reset($r);
        }
        return $arr;
    }

    function firstRow($sql, $params=[]) {
        $res = $this->adapter->get($sql, $params);
        if(!is_array($res)) return false;
        return reset($res);
    }

    protected function checkPlausibleTable($table) {
        $res = ctype_alnum(str_replace('_', '', $table));
        if(!$res) throw new CubeException("'$table' is no valid table name!");
    }

    function setDefaults() {
        if(!$this->idProvider) {
            $this->idProvider = new RoboID();
        }
        if(!$this->queryWriter) {
            $this->queryWriter = new QueryWriter();
        }
    }

    function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    function setQueryWriter($writer) {
        $this->queryWriter = $writer;
    }

    function setIdProvider($provider) {
        $this->idProvider = $provider;
    }
}
