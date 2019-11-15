<?php class Database
{

    private $dbConnection;
    private $host = "localhost";
    private $database = "phpcw";
    private $username = "root";
    private $password = "";


    public function __construct()
    {
        try {
            $this->dbConnection = new PDO("mysql:host=$this->host;dbname=$this->database;charset=utf8", $this->username, $this->password)
                or die("There was a problem connecting to the database.");
            $this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            /* check connection */
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return true;
    }


    /**
     * On error returns an array with the error code.
     * On success returns an array with multiple mysql data.
     * 
     * @param string $query
     * @return array
     */
    public function query($query)
    {
        /* array returned, includes a success boolean */
        $return = array();

        if (!$result = $this->mysqli->query($query)) {
            $return['success'] = false;
            $return['error'] = $this->mysqli->error;

            return $return;
        }

        $return['success'] = true;
        $return['affected_rows'] = $this->mysqli->affected_rows;
        $return['insert_id'] = $this->mysqli->insert_id;

        if (0 == $this->mysqli->insert_id) {
            $return['count'] = $result->num_rows;
            $return['rows'] = array();
            /* fetch associative array */
            while ($row = $result->fetch_assoc()) {
                $return['rows'][] = $row;
            }

            /* free result set */
            $result->close();
        }

        return $return;
    }

    public function dontWorryQuery($sql, $moreThanOne = true)
    {

        if ($stmt = $this->dbConnection->query($sql)) {
            if ($moreThanOne) {
                $data = [];
                foreach ($stmt as $row) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return false;
    }

    public function getDbConnection()
    {
        return $this->dbConnection;
    }

    public function findGroupsAvailable()
    {
        $data = [];
        $sql = "SELECT COUNT(ID), group_number
        FROM students
        GROUP BY group_number
        ORDER BY COUNT(ID) ASC";
        $result = $this->dbConnection->query($sql);
        foreach ($result as $row) {
            if ($row['COUNT(ID)'] == 3) {
                $data[] = $row;
            }
        }
        return $data;
    }

    function getUserDetails($ID)
    {
        $sql = "SELECT ID, pass, role FROM users WHERE ID = ?";
        if ($stmt = $this->dbConnection->prepare($sql)) {
            if ($stmt->execute([$ID])) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return false;
    }

    function getPeers($number, $ID)
    {
        $data = [];
        $sql = "SELECT ID FROM students WHERE group_number = ?";
        if ($stmt = $this->dbConnection->prepare($sql)) {
            if ($stmt->execute([$number])) {
                foreach ($stmt as $row) {
                    if ($row['ID'] != $ID) {
                        $data[] = $row;
                    }
                }
                return $data;
            }
        }
        return false;
    }

    function addStudent($ID, $email, $password, $group_number)
    {
        $sql = "INSERT INTO users (ID, email, pass) VALUES (?, ?, ?)";
        if ($stmt = $this->dbConnection->prepare($sql)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt->execute([$ID, $email, $password])) {
                $sql = "INSERT INTO students (ID, group_number) VALUES (?, ?)";
                if ($stmt = $this->dbConnection->prepare($sql)) {
                    if ($stmt->execute([$ID, $group_number])) {
                        if ($others = $this->getPeers($group_number, $ID)) {
                            foreach ($others as $student) {
                                $colleagueID = $student["ID"];
                                $sql = "INSERT INTO assessments (grader_id, graded_id) VALUES ($colleagueID, $ID)";
                                $this->dbConnection->query($sql);
                                $sql = "INSERT INTO assessments (grader_id, graded_id) VALUES ($ID, $colleagueID)";
                                $this->dbConnection->query($sql);
                            }
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Automatically closes the mysql connection
     * at the end of the program.
     */
    public function __destruct()
    {
        $this->dbConnection = null;
    }
}
