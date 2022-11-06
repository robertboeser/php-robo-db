<?php
namespace Robo\RoboDB;

class CubeDB {
    protected $repository;
    protected $table_cols = [];

    function __construct($repo=null) {
        if(!$repo) $repo = new Repository();
        $this->repository = $repo;
    }

    function setup($cfg) {
        if(is_object($cfg)) {
            $this->repository->setAdapter($cfg);
        } else {
            $adapter = new Adapter();
            $adapter->setup($cfg);
            $this->repository->setAdapter($adapter);
        }
        $this->repository->setDefaults();
    }

    function findCube($table, $where='', $params=[]) {
        $res = $this->repository->findOne($table, $where, $params);
        if(!$res) return null;
        return $this->newCube($table, $res[0]);
    }

    function findCubes($table, $where='', $params=[]) {
        $res = $this->repository->find($table, $where, $params);
        if(!$res) return [];
        $arr = [];
        foreach($res as $r) {
            $arr[] = $this->newCube($table, $r);
        }
        return $arr;
    }

    function newCube($table, $data=[]) {
        $cols = $this->fetchTableCols($table);
        return new Cube($table, $cols, $data);
    }

    function addCube($cube) {
        $id = $this->repository->add($cube->getTable(), $cube->getData(), $migrate);
        if(!$id) return false;
        $cube->id = $id;
        return $id;
    }

    function updCube($cube) {
        return $this->repository->upd($cube->getTable(), $cube->getData(), $migrate);
    }

    function delCube($cube) {
        return $this->repository->del($cube->getTable(), $cube->id);
    }

    function setIdProvider($provider) {
        // $provider must implement genID() method
        $this->repository->setIdProvider($provider);
    }

    function setQueryWriter($writer) {
        $this->repository->setQueryWriter($writer);
    }

    function fetchTableCols($table) {
        if(empty($this->table_cols[$table])) {
            $cols = $this->repository->fetchTableCols($table);
            $this->table_cols[$table] = $cols;
        }
        return $this->table_cols[$table];
    }
}
