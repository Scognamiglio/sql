<?php
namespace Sql;
use Exception;
use PDO;

class Sql
{
    private static $bdd;
    private static $oldAcces;
    private static $timeReco = 120;
    private static $dataBdd = [];

    public static function setDataBdd($dataBdd){
        self::$dataBdd = $dataBdd;
    }

    private static function connexion(){

        self::$bdd = null;
        $dataBdd = self::$dataBdd;
        try
        {
            self::$bdd = new PDO("mysql:host={$dataBdd['host']};dbname={$dataBdd['dbname']};charset=utf8", $dataBdd['user'], $dataBdd['passwd']);
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
    }


    public static function query($qry){
        if(empty(self::$bdd) || !empty(self::$oldAcces) && (time() - self::$oldAcces) > self::$timeReco)
            self::connexion();

        $result = self::$bdd->query($qry);
        self::$oldAcces = time();
        return $result;
    }

    public static function fetch($qry){
        $result = self::query($qry);
        return $result->fetch();
    }

    public static function fetchAll($qry,$onlyAssoc=0){
        $result = self::query($qry);
        return $result->fetchAll($onlyAssoc ? PDO::FETCH_ASSOC : 0);
    }

    // case sensitif !
    public static function createArrayOrder($qry,$key){
        $results = sql::fetchAll($qry,PDO::FETCH_ASSOC);
        $return = [];
        foreach ($results as $result){
            $tmpKey = $result[$key];
            unset($result[$key]);
            $return[$tmpKey] = $result[array_keys($result)[0]];
        }
        return $return;
    }

    public static function getJsonBdd ($qry){
        $r = sql::fetch($qry);
        return empty($r) ? false : json_decode($r[0],true);
    }
}