<?php
namespace Robo\RoboCube;

class CubeDB {
    protected $repository;
    protected $table_cols = [];

    function __construct($repo=null) {
        if(!$repo) $repo = new Repository();
        $this->repository = $repo;
    }

    function setup($dsn, $usr='', $pwd='', $opts=null) {
        if(is_object($dsn)) {
            $this->repository->setAdapter($dsn);
        } else {
            $adapter = new Adapter();
            $adapter->setup($dsn, $usr, $pwd, $opts);
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
        return new CubeEntity($table, $cols, $data);
    }

    function addCube($cube, $migrate=false) {
        $id = $this->repository->add($cube->getTable(), $cube->getData(), $migrate);
        if(!$id) return false;
        $cube->id = $id;
        return $id;
    }

    function updCube($cube, $migrate=false) {
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
            $this->table_cols[$table] = $this->repository->fetchTableCols($table);
        }
        return $this->table_cols[$table];
    }
}
