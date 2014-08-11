<?php
defined('ABSPATH') or die("No script kiddies please!");

class ClanRegistration {

    var $dbh;

    public function __construct()
    {
        $this->dbh = $this->dbConn();
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

}


/* $app = new ClanRegistration(); */

/* if (!empty($_GET['action']) && method_exists($app, $_GET['action'])) { */
/*     $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS); */
/*     $app->$action(); */
/* } */