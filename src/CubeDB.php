<?php
namespace Robo\RoboDB;

class CubeDB {
    protected $repository;
    protected $table_cols = [];

    protected function foundCube($table, $data=[]) {
        $cols = $this->fetchTableCols($table);
        $cube = new Cube($table, $cols);
        $cube->setDataDB($data);
        return $cube;
    }

    protected function checkMatch($cube, $need_match=[]) {
        $match = true;
        if($need_match) {
            $cub2 = $this->findCube($cube->getTable(), 'id = ?', [$cube->id]);
            foreach($need_match as $nm) {
                if($cube->get($nm) !== $cub2->get($nm)) $match = false;
            }
        }
        return $match;
    }

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
        return $this->foundCube($table, $res[0]);
    }

    function findCubes($table, $where='', $params=[]) {
        $res = $this->repository->find($table, $where, $params);
        if(!$res) return [];
        $arr = [];
        foreach($res as $r) {
            $arr[] = $this->foundCube($table, $r);
        }
        return $arr;
    }

    function newCube($table, $data=[]) {
        $cols = $this->fetchTableCols($table);
        $cube = new Cube($table, $cols);
        $cube->setData($data);
        return $cube;
    }

    function addCube($cube, $keep_id = false) {
        $id = $this->repository->add($cube->getTable(), $cube->getDataDB(), $keep_id);
        if(!$id) return false;
        $cube->id = $id;
        return $id;
    }

    function putCube($cube) {   // add Cube with PushID // deprecated
        $id = $this->repository->add($cube->getTable(), $cube->getDataDB(), true);
        if(!$id) return false;
        $cube->id = $id;
        return $id;
    }

    function pushCube($cube, $need_match = []) {  // add or update
        $id = $this->addCube($cube, true);
        if($id) return $id;

        if(!$this->checkMatch($cube, $need_match)) return false;
        return $this->updCube($cube);
    }

    function updCube($cube) {
        return $this->repository->upd($cube->getTable(), $cube->getDataDB());
    }

    function delCube($cube, $need_match = []) {
        if(!$this->checkMatch($cube, $need_match)) return false;
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
