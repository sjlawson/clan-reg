<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

//header('Content-Type: application/json');
require_once('../wp-config.php');
defined('ABSPATH') or die("No script kiddies please!");

class ClanRegistrationTasks {

    private $dbh;
    private $urlParams;

    public function __construct()
    {
        $this->dbh = $this->dbConn();
        $this->urlParams = $_REQUEST;
    }

    private function dbConn()
    {
        /* Connect to an ODBC database using driver invocation */
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;

        try
            {
                $dbh = new PDO($dsn, DB_USER, DB_PASSWORD);
            } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        return $dbh;
    }

    /**
     * return PDOStatement containing registered clans for $year
     * @param $year
     * @return PDOStatement
     */
    public function getRegisteredClans($year)
    {
        $query = "SELECT * FROM `clan_registrations` cr WHERE cr.`year` = :year ORDER BY clanName";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':year', $year, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Load registered clan names for current year, or next year if NOW is after October
     * return $markup - html table
     */
    public function printCurrentRegistrations()
    {
        $currYear = date('m') > 10 ? date('Y') + 1 : date('Y');
        $stmt = $this->getRegisteredClans($currYear);

        $markup = "<table>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $markup .= "<tr><td>" . $row['clanName'] . "</td></tr>";
        }
        $markup .= "</table>";

        return $markup;
    }

    /**
     * Save clan registration from form data
     * return boolean true on success
     */
    public function registerClan()
    {
        $currYear = date('m') > 10 ? date('Y') + 1 : date('Y');
        $params = array(
            ':clanName' => $this->urlParams['clanName'],
            ':contactPerson' => $this->urlParams['contactPerson'],
            ':phone' => $this->urlParams['phone'],
            ':email' => $this->urlParams['email'],
            ':address' => $this->urlParams['address'],
            ':city' => $this->urlParams['city'],
            ':state' => $this->urlParams['state'],
            ':zip' => $this->urlParams['zip'],
            ':electrical'  => $this->urlParams['electrical'],
            ':tables' => $this->urlParams['tables'],
            ':chairs' => $this->urlParams['chairs'],
            ':notes' => $this->urlParams['notes'],
            ':amountPayable' => $this->urlParams['amount'],
            ':year' => $currYear
        );

        $query = "INSERT INTO clan_registrations
            (
                `clanName`, `contactPerson`, `phone`,
                `email`,`address`,`city`,`state`,`zip`,
                `electrical`,`tables`,`chairs`,`notes`,
                `amountPayable`, `year`
            ) VALUES (
                :clanName, :contactPerson, :phone,
                :email, :address, :city, :state, :zip,
                :electrical, :tables, :chairs, :notes,
                :amountPayable, :year
            )";

        $stmt = $this->dbh->prepare($query);

        return $stmt->execute($params);
    }

    /**
     * Get clan information by name is registered during the current year
     * @return array
     */
    public function getClanByClanName()
    {
        if(empty($this->urlParams['clanName']) ) {
            echo json_encode(array());
        }

        $currYear = date('m') > 10 ? date('Y') + 1 : date('Y');
        $query = "SELECT * FROM `clan_registrations` cr
                WHERE
                        cr.`clanName` = :clanName AND cr.`year` = :year
                LIMIT 1";

        $stmt = $this->dbh->prepare($query);
        $stmt->bindValue(':clanName', $this->urlParams['clanName'], PDO::PARAM_STR );
        $stmt->bindValue(':year', $currYear, PDO::PARAM_STR);
        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($dataRow);
    }

}

$app = new ClanRegistrationTasks();

// echo var_dump(method_exists($app, $_GET['action']));

if (!empty($_GET['action']) && method_exists($app, $_GET['action'])) {
    $action = $_GET['action'];
    $app->$action();
}