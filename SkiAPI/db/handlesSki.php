<?php


class handlesSki extends db
{

    /**
     * Function that will create a transition record for a ski. When it is created and assigned to a ski.
     *
     * @param int $productID the product id we would like to add transition records.
     */
    function addTransition(int $productID){
        $token = (new AuthorisationModel()) ->extractID($_COOKIE['auth_token']);
        $this->db->beginTransaction();
        $query = "INSERT INTO `handlesski`(`employeeNumber`, `productID`) 
        VALUES (:empNumber, :productID)";
        $stmt = $this->db->prepare($query);
        $stmt -> bindValue(':empNumber', $token);
        $stmt -> bindValue(':productID', $productID);
        $stmt -> execute();
        $this ->db->commit();
    }


}