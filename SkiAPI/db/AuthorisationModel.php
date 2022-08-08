<?php
require_once 'DB.php';

/**
 * Class AuthorisationModel
 */
class AuthorisationModel extends DB
{
    public function __construct()
    {
        parent::__construct();


    }

    /**
     * A simple authorisation mechanism - just checking that the token matches the one in the database
     * @param string $token
     * @return bool indicating whether the token was successfully verified
     */
    public function isValid(string $token): bool
    {
        if (!preg_match('~[0-9]+~', $token)) {
            return false;
        }
        $query = 'SELECT COUNT(*) FROM auth_token WHERE token = :token';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        $idFromToken = $this->extractID($token);
        return $this->IsValidID($idFromToken, $token);
    }


    /**
     * Function that is separating the id from the token. To know which of the customer, employee or transporter
     * that is in the system.
     *
     * @param string $token the cookie that is set.
     * @return string the id.
     */
    public function extractID(string $token): string
    {
        $id = "";
        for ($i = 0; $i <= strlen($token) - 1; $i++) {
            if (is_numeric($token[$i])) {
                $id .= $token[$i];
            }
        }
        return $id;
    }

    /**
     * Function that is separating the string id from the token, to know which type of user that is
     * currently using the system.
     *
     * @param string $token the cookie that is set.
     * @return string the id.
     */
    public function extractString(string $token): string
    {
        $id = "";
        for ($i = 0; $i <= strlen($token) - 1; $i++) {
            if (!is_numeric($token[$i])) {
                $id .= $token[$i];
            }
        }
        return $id;
    }


    /**
     * Function that will determine what kind of user you are.
     * If you are a transporter, employee, or a customer.
     *
     * @param string $token is the cookie
     * @return string is the type of user you are.
     */
    public function getUser(string $token): string{
        $tokenTrimmed = $this ->extractString($token);
        $res = "";
        $query = "SELECT `User` FROM `auth_token` WHERE token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':token', $tokenTrimmed);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $res = $row['User'];
        }
        return $res;
    }


    /**
     * Function that will determine if the integer value of the cookie is representing
     * a valid person, that is in the system.
     *
     * @param int $id is the integer id that determines who you are.
     * @param string $token the authorisation cookie
     * @return bool true if it is a valid user.
     *              false if the user id is not valid
     */
    public function IsValidID(int $id, string $token): bool{
        $res = $this -> getUser($token);
        if ($res == 'Employee') {
            $query1 = "SELECT count(*) FROM `employee` where employeeNumber = :id";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bindValue(':id', $id);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_NUM);
            if ($row[0] == 0) {
                print "No valid ID";
                return false;
            } else {
                return true;
            }
        } elseif($res == 'Customer') {
            $query1 = "SELECT count(*) FROM `customer` where customerID = :id";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bindValue(':id', $id);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_NUM);
            if ($row[0] == 0) {
                print "No valid ID";
                return false;
            } else {
                return true;
            }
        }elseif ($res == 'TransporterEndpoint'){
            $query1 = "SELECT count(*) FROM `transporter` where transporterID = :id";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bindValue(':id', $id);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_NUM);
            if ($row[0] == 0) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }


    /**
     * Function that will determine if you have access to the different endpoints.
     *
     * @param string $token the authorisation of the user.
     * @param string $endpoint The endpoint the user is trying to access.
     * @return bool true if you have access.
     */
    public function hasAccess(string $token, string $endpoint):bool{
        $tokenTrimmed = $this ->extractString($token);
        $user = $this -> getUser($tokenTrimmed);
        $endpoint = explode("/", $endpoint);
        if ($user == 'Customer'){
            if ($endpoint[0] ==  RESTConstants::ENDPOINT_CUSTOMERUSER){
                return true;
            }
        }elseif ($user == 'Employee'){
            if ($endpoint[0] == RESTConstants::ENDPOINT_CUSTOMER ){
                return true;
            }
        }elseif ($user == 'TransporterEndpoint'){
            if ($endpoint[0] == RESTConstants::ENDPOINT_TRANSPORTER ){
                return true;
            }
        }
        return false;
    }


    /**
     * A function that will determine what type of employee you are.
     * Such as Customer representative, storekeeper and production planner.
     *
     * @param string $token that is going to determine which of the employee type you are.
     * @return string that will see what type of employee you are.
     */
    public function employeeType(string $token): string{
        $tokenTrimmed = $this ->extractID($token);
        $res ="";
        $query1 = "SELECT `department` FROM `employee` WHERE `employeeNumber` = :id";
        $stmt1 = $this->db->prepare($query1);
        $stmt1->bindValue(':id', $tokenTrimmed);
        $stmt1->execute();
        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
            $res = $row['department'];
        }
        if ($res != ""){
            return $res;
        }
    }

    /**
     * Function that will check if the order you are searching for is your order.
     *
     * @param int $order the order we want to check is yours
     * @return bool true if the order is yours
     */
    public function isYourOrder(int $order): bool{
        $res = array();
        $customerID = $this -> extractID($_COOKIE['auth_token']);
        $query = "SELECT `orderNumber` FROM `orders` WHERE customerID = :id";
        $stmt = $this -> db ->prepare($query);
        $stmt -> bindValue(':id', $customerID);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $res = array(
                $row['orderNumber']
            );
        }
        for ($i = 0; $i < count($res); $i++){
            if ($res[$i] == $order){
                return true;
            }
        }
        return false;
    }

}