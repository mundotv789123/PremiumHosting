<?php

namespace app\system;

use PDO;

class DB extends Config
{

    private $con;

    public function __construct() {
        try {
            $this->con = new PDO("mysql:host=".self::DBHOST.";dbname=".self::DBNAME, self::DBUSER, self::DBPASS);
            $this->con->exec("set names utf8");
        } catch (\PDOException $e) {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function getConnection()
    {
        return $this->con;
    }

    public function set($host, $user, $pass, $db) {
        try {
            $this->con = new PDO("mysql:host=".$host.";dbname=".$db, $user, $pass);
            $this->con->exec("set names utf8");
        } catch (\PDOException $e) {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function query($sql)
    {
        $stmt = $this->con->prepare($sql);
        if(!$stmt->execute())
        {
            $erros = "";
            foreach ($stmt->errorInfo() as $error)
            {
                $erros .= $error." - ";
            }
            define('APP_ERROR', $erros);
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function select($sql, $data = null)
    {
        try {
            $state = $this->con->prepare($sql);

            if($data == null)
            {
                $state->execute();
            }else{
                $state->execute($data);
            }

            return $state->fetchAll(PDO::FETCH_OBJ);
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function countTable($table)
    {
        try {
            $state = $this->con->prepare("SELECT * FROM {$table}");
            $state->execute();

            return $state->rowCount();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function insert($obj, $table)
    {
        try {
            $variables = "";

            for($i=0; $i<count($obj); $i++) {
                $variables .= "?,";
            }

            $variables = substr($variables, 0, -1);

            $ids = [];
            $idsS = "";
            $values = [];

            foreach($obj as $key => $value)
            {
                $ids[] = $key;
                $values[] = $value;

                $idsS .= "`{$key}`,";
            }

            $idsS = substr($idsS, 0, -1);

            $sql = "INSERT INTO `{$table}`({$idsS}) VALUES ({$variables})";

            $state = $this->con->prepare($sql);
            $state->execute($values);
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
        return [ 'success' => 'true', 'feedback'=>'', 'id'=>$this->last($table) ];
    }

    public function update($obj, $condition, $table)
    {
        try {
            $data = [];
            $where = [];

            foreach ($obj as $ind => $val)
            {
                $data[] = "`{$ind}` = ".(is_null($val) ? "NULL" : "'{$val}'");
            }
            foreach ($condition as $ind => $val)
            {
                $where[] = "`{$ind}` ".(is_null($val) ? "IS NULL" : " = '{$val}'");
            }

            $sql = "UPDATE `{$table}` SET ".implode(',', $data)." WHERE ".implode(' AND', $where);

            $state = $this->con->prepare($sql);
            $state->execute(array('widgets'));
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }

        return [ 'success'=>true, 'feedback'=>'' ];
    }

    public function delete($condition, $table)
    {
        try {
            $where = [];

            foreach ($condition as $ind => $val)
            {
                $where[] = "`{$ind}` ".(is_null($val) ? "IS NULL" : " = '{$val}'");
            }

            $sql = "DELETE FROM {$table} WHERE ".implode(' AND', $where);

            $state = $this->con->prepare($sql);
            $state->execute(array('widgets'));
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
    }

    public function last($table)
    {
        try {
            $state = $this->con->prepare("SELECT last_insert_id() as last FROM `{$table}`");
            $state->execute([$table]);
            $state = $state->fetchObject();
        }catch (\PDOException $e)
        {
            define('APP_ERROR', $e->getMessage());
            include("./app/templates/system/error_page.phtml");
            die();
        }
        return $state->last;
    }

    public function closeConnection() {
        $this->con = null;
    }

}