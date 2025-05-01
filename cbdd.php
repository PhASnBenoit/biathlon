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

    public function saveJudge($runnerName, $judgeName, $num, $token) {
        $sql = "UPDATE race SET runnerName = :runnerName, judgeName = :judgeName, token = :token WHERE num = :num";
        $result = $this->update($sql, ['runnerName' => $runnerName, 'judgeName' => $judgeName, 'num' => $num, 'token' => $token]);
        if ($result === 0) {
            $sql = "INSERT INTO race (num, runnerName, judgeName, token) VALUES (:num, :runnerName, :judgeName, :token)";
            $result = $this->insert($sql, ['runnerName' => $runnerName, 'judgeName' => $judgeName, 'num' => $num, 'token' => $token]);
            return $result;  // c'est le lastInsertId
        } // rowCount=0
        return $result; // rowCount
    } // saveRace

    public function getRace() {
        $sql = "SELECT * FROM race";
        $result = $this->select($sql);
        return $result;
    } // getJuges

    public function setTokenArbitre($token) {
        $sql = "UPDATE config SET token = :token WHERE id_config = 1";
        $result = $this->update($sql, ['token' => $token]);
        return $result;
    } // setTokenArbitre

    public function getTokenArbitre() {
        $sql = "SELECT token FROM config LIMIT 1";
        $result = $this->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['token'];
    } // getState

    public function getState() {
        $sql = "SELECT state FROM activity LIMIT 1";
        $result = $this->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['state'];
    } // getState

    public function setState($etat) {
        $sql = "UPDATE activity SET state = :etat WHERE id_activity = 1";
        $result = $this->update($sql, ['etat' => $etat]);
        return $result;
    } // setState

    public function getNbJuges() {
        $sql = "SELECT nb_juges, max_juges FROM config LIMIT 1";
        $result = $this->select($sql);
        return $result;
    } // getNbJuges

    public function viderTableRace() {
        $sql = "TRUNCATE TABLE race";
        $result = $this->pdo->exec($sql);
        return $result;
    } // viderTableRace

    public function setNbJuges($nb) {
        $sql = "UPDATE config SET nb_juges = :nb WHERE id_config = 1";
        $result = $this->update($sql, ['nb' => $nb]);
        return $result;
    } // setNbJuges

    public function getParamsCourse() {
        $stmt = $this->select("SELECT * from config LIMIT 1");
        return $stmt;
    }

    public function lockTable($table, $mode) {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("LOCK TABLES $table $mode");
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }

    } // lockTable

    public function unlockTable() {
        try {
            // Déverrouillage des tables
            $this->pdo->exec("UNLOCK TABLES");
            // Fin de la transaction
            $this->pdo->commit();
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    } // lockTable

////////////////////////////////////////////////////////////////////////////////////////////////

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
