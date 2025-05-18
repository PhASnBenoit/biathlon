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

    public function isGoodCode($code) {  // ARBITRE
        $sql = "SELECT COUNT(*) FROM config WHERE code = :code";
        $result = $this->select($sql, ['code' => $code]);
        $exists = $result->fetchColumn();
        return $exists;
    } // isGoodCode

    public function set_t0($t0) {  // ARBITRE
        $sql = "UPDATE race SET t0 = :t0";
        $result = $this->update($sql, ['t0' => $t0]);
        return $result;
    } // set_t0

    public function set_judgeState($no, $etat) {  // JUGE
        $sql = "UPDATE race SET judgeState = :etat WHERE num=:no";
        $result = $this->update($sql, ['etat' => $etat, 'no' => $no]);
        return $result;
    } // set_judgeState

    public function saveParamsRace($raceName, $judgeCount) {  // ARBITRE
        $sql = "UPDATE config SET nom_course = :raceName, max_juges = :judgeCount WHERE id_config = 1";
        $result = $this->update($sql, ['raceName' => $raceName, 'judgeCount' => $judgeCount]);
        return $result;
    } // saveParamsRace

    public function addJudge($runnerName, $judgeName, $num, $token) {  // JUGE
        $sql = "UPDATE race SET runnerName = :runnerName, judgeName = :judgeName, token = :token WHERE num = :num";
        $result = $this->update($sql, ['runnerName' => $runnerName, 'judgeName' => $judgeName, 'num' => $num, 'token' => $token]);
        if ($result === 0) {
            $sql = "INSERT INTO race (num, judgeState, runnerName, judgeName, token) VALUES (:num, 0, :runnerName, :judgeName, :token)";
            $result = $this->insert($sql, ['runnerName' => $runnerName, 'judgeName' => $judgeName, 'num' => $num, 'token' => $token]);
            return $result;  // c'est le lastInsertId
        } // rowCount=0
        return $result; // rowCount
    } // addJudge

    public function getRace() {  // PUBLIC
        $sql = "SELECT * FROM race";
        $result = $this->select($sql);
        return $result;
    } // getRace

    public function setTokenArbitre($token) {  // ARBITRE
        $sql = "UPDATE config SET token = :token WHERE id_config = 1";
        $result = $this->update($sql, ['token' => $token]);
        return $result;
    } // setTokenArbitre

    public function setTokenJuges($token) {
        $sql = "UPDATE race SET token = :token";
        $result = $this->update($sql, ['token' => $token]);
        return $result;
    } // setTokenArbitre

    public function get_t0() {  // ARBITRE
        $sql = "SELECT t0 FROM race LIMIT 1";
        $result = $this->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['t0'];
    } // getTokenArbitre

    public function getTokenArbitre() {  // ARBITRE
        $sql = "SELECT token FROM config LIMIT 1";
        $result = $this->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['token'];
    } // getTokenArbitre

    public function getTokenJuge($token) {  // JUGE
        $sql = "SELECT token, num FROM race WHERE token = :token LIMIT 1";
        $result = $this->select($sql, ['token' => $token]);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        if ($row)
            return ['token' => $row['token'], 'num' => $row['num']];
        else
            return ['token' => "", 'num' => 0];
    } // getTokenJuge

    public function getState() {
        $sql = "SELECT state FROM activity LIMIT 1";
        $result = $this->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['state'];
    } // getState

    public function get_judgeState($num) {  // PUBLIC
        $sql = "SELECT judgeState FROM race WHERE num=:num";
        $result = $this->select($sql, ['num'=>$num]);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['judgeState'];
    } // get_judgeState

    public function setState($etat) {
        $sql = "UPDATE activity SET state = :etat WHERE id_activity = 1";
        $result = $this->update($sql, ['etat' => $etat]);
        return $result;
    } // setState

    public function setTime($numJuge, $judgeState, $chrono) {
        $t = "t$judgeState";
        if ($judgeState==10)
            $t = "totalTime";
        $sql = "UPDATE race SET judgeState = :js, $t = :t  WHERE num = :num";
        $result = $this->update($sql, ['js' => $judgeState, 't' => $chrono, 'num' => $numJuge]);
        return $result;
    } // setTime

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

    public function getParamsJuge($num) {  // JUGE
        $sql = "SELECT * from race WHERE num = :num";
        $stmt = $this->select($sql, ['num' => $num]);
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
