<?php


class handleOrder extends db
{

    /**
     * Function that will create a transition record for an order.
     * When its state is changed.
     *
     * @param int $orderNumber of the order we would like to create a record on.
     * @param string $comment of the action done on the order.
     */
    function addTransition(int $orderNumber, string $comment){
        $auth = new AuthorisationModel();
        $this->db->beginTransaction();
        $query = "INSERT INTO `handlesorder`(`employeeNumber`, `orderNumber`, `comment`)
                VALUES (:empNumber, :orderNumber, :comment)";
        $stmt = $this->db->prepare($query);
        $employee = $auth ->extractID($_COOKIE['auth_token']);
        $stmt -> bindValue(':empNumber', $employee );
        $stmt -> bindValue(':orderNumber', $orderNumber);
        $stmt -> bindValue(':comment', $comment);
        $stmt -> execute();
        $this ->db->commit();
    }
}


