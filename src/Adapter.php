<?php
namespace Robo\RoboDB;

use PDO;
use PDOException;

class Adapter {
    protected $cfg;
    protected $pdo;

    protected $fetchMode = PDO::FETCH_ASSOC;

    function setup($cfg) {
        $this->cfg = $cfg;
    }

    function exec($sql, $params=[], $options=[]) {
        $pdo = $this->connect();
        $sth = $pdo->prepare($sql, $options);
        $res = $sth->execute($params);
        if(!$res) return false;
        return $sth;
    }

    function get($sql, $params=[], $options=[]) {
        $sth = $this->exec($sql, $params, $options);
        if(!$sth) return [];
        $sth->setFetchMode($this->fetchMode);
        return $sth->fetchAll();
    }

    function close() {

    }

    protected function connect() {
        $dsn = $this->buildDSN($this->cfg);
        $usr = $this->cfg['user'];
        $pwd = $this->cfg['pass'];

        try {
            if(!$this->pdo) {
                $this->pdo = new PDO($dsn, $usr, $pwd);
            }
        }
        catch(PDOException $ex) {
            throw new CubeException('cannot connect to database');
        }

        return $this->pdo;
    }

    protected function buildDSN($cfg) {
        $query = ["host={$cfg['host']}"];
        if(!empty($cfg['port'])) $query[] = "port={$cfg['port']}";
        if(!empty($cfg['name'])) $query[] = "dbname={$cfg['name']}";
        if(!empty($cfg['charset'])) $query[] = "charset={$cfg['charset']}";
        return $cfg['adapter'].':'.implode(';', $query);
    }
}
