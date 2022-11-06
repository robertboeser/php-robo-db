<?php
namespace Robo\RoboDB;

use PDO;

class Adapter {
    protected $dsn;
    protected $usr;
    protected $pwd;
    protected $opt;
    protected $pdo;

    protected $fetchMode = PDO::FETCH_ASSOC;

    function setup($dsn, $user='', $pass='', $opts=null) {
        $this->dsn = $dsn;  // dsn in URL format: scheme://user:pass@host:port/database
        $this->usr = $user;
        $this->pwd = $pass;
        $this->opt = $opts;
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
        $props = parse_url($this->dsn);
        // mysql:host=localhost;dbname=database
        $path = trim($props['path'], '/');

        $dsn = "{$props['scheme']}:host={$props['host']};dbname=$path";
        $usr = $props['user'];
        $pwd = $props['pass'];
        if($this->usr) $usr = $this->usr;
        if($this->pwd) $pwd = $this->pwd;
        $opt = $this->opt;

        if(!$this->pdo)
            $this->pdo = new PDO($dsn, $usr, $pwd, $opt);
        return $this->pdo;
    }
}
