<?php
require 'setBdd.inc.php';

class CBdd {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
    }

    public function isGoodCode($code) {
        $sql = "SELECT COUNT(*) FROM config WHERE code = :code";
        $result = $this->select($sql, ['code' => $code]);
        $exists = $result->fetchColumn();
        return $exists;
    } // isGoodCode

    public function saveRace($raceName, $judgeCount) {
        $sql = "UPDATE config SET nom_course = :raceName, max_juges = :judgeCount WHERE id_config = 1";
        $result = $this->update($sql, ['raceName' => $raceName, 'judgeCount' => $judgeCount]);
        return $result;
    } // saveRace

// A CORRIGER !!!
    public function saveJudge($runnerName, $judgeName) {
        $sql = "UPDATE config SET nom_course = :raceName, max_juges = :judgeCount WHERE id_config = 1";
        $result = $this->update($sql, ['raceName' => $raceName, 'judgeCount' => $judgeCount]);
        return $result;
    } // saveRace

    public function getState() {
        $sql = "SELECT state FROM activity LIMIT 1";
        $result = $this->select($sql);
        return $result;
    } // getState

    public function setState($etat) {
        $sql = "UPDATE activity SET state = :etat WHERE id_activity = 1";
        $result = $this->update($sql, ['etat' => $etat]);
        return $result;
    } // setState

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    private function select($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            //return $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stmt;
        } catch (PDOException $e) {
            die("Erreur dans la requête SELECT : " . $e->getMessage());
        }
    }

    private function insert($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Erreur dans la requête INSERT : " . $e->getMessage());
        }
    }

    private function update($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Erreur dans la requête UPDATE : " . $e->getMessage());
        }
    }

    private function delete($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Erreur dans la requête DELETE : " . $e->getMessage());
        } // catch
    } // delete
} // class

// Création d'une instance de la classe Database
$db = new CBdd($host, $bddname, $username, $password);

?>
