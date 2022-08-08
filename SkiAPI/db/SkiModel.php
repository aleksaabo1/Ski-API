<?php

require_once 'handlesSki.php';



class SkiModel extends db
{

   /* function getCollection(array $query = null): array
    {
        $res = array();
        $query = "SELECT `productID`, `weight`, `length`, `type`";
        $stmt = $this -> db ->prepare($query);
        $stmt -> execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array('productID' => intval($row['productID']),
                'weight' => $row['weight'],
                'length' => $row['length'],
                'type' => $row['type']);
        }
        return $res;
    }*/


    /**
     * A function that will return information of a ski.
     *
     * @param int $id the product id of a ski
     * @return array that contains the information of the a specific ski
     */
    function getResource(int $id): array
    {
        $res = array();
        $query = 'SELECT `productID`, `skiID`, `length`, `weight` FROM `ski` WHERE productID = :productID';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':productID', $id);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'productID' => $row['productID'],
                'skiID' => $row['skiID'],
                'weight' => $row['weight'],
                'length' => $row['length']
            );
        }
        return $res;
    }


    /**
     * A function that is creating new skis, into the system.
     *
     * @param array $resource is the given information.
     * @return array is the newly created ski.
     */
    function createResource(array $resource): array
    {
        $handles = new handlesSki();
        $productID = rand(0, 100000);
        $this->db->beginTransaction();
        $query = 'INSERT INTO ski (productID, ski.skiID, ski.weight, ski.length) 
                VALUES (:productID, :skiID, :weight, :length)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':productID', $productID);
        $stmt->bindValue(':skiID', $resource['skiID']);
        $stmt->bindValue(':weight', $resource['weight']);
        $stmt->bindValue(':length', $resource['length']);
        $stmt->execute();
        $this->db->commit();
        $handles ->addTransition($productID);
        $res = $this->getResource($productID);
        return $res;
    }


    /**
     * A function that will delete a given ski from the system, when it has been shipped.
     *
     * @param int $id is the shipment number.
     */
    function deleteResource(int $id)
    {
        $res = array();
        $this -> db -> beginTransaction();
        $query = 'Select productID FROM ski 
                 LEFT OUTER join shipment ON reservedToOrder = orderNumber
                  where shipment.shipmentNumber = :shipmentNr';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':shipmentNr', $id);
        $stmt ->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array('productID' => $row['productID']);
        }
        $deleteQuery = "DELETE FROM `ski` WHERE productID = :id";
        $stmt1 = $this->db->prepare($deleteQuery);
        for ($i = 0 ; $i < count($res); $i++) {
            $stmt1->bindValue(':id', $res[$i]['productID']);
            $stmt1->execute();
        }
        $this->db->commit();
    }


    /**
     * A function that will return skis that are assigned to a given order.
     *
     * @param int $orderNumber to the order we want to get the reserved skis
     * @return array an array of the skis that is reserved to a given order
     */
    public function reserved(int $orderNumber): array{
        $getSkisInOrder = "SELECT `reservedToOrder`, `skiID`, `length`, `weight` FROM `ski` WHERE reservedToOrder = :orderNr";
        $stmt = $this->db->prepare($getSkisInOrder);
        $stmt -> bindValue(':orderNr', $orderNumber);
        $stmt -> execute();

        $Skis = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $Skis[] = array(
                'orderNumber' => $row['reservedToOrder'],
                'skiID' => $row['skiID'],
                'length' => $row['length'],
                'weight' => $row['weight']);
        }
        return $Skis;
    }

    /**
     * Function that will determine if the given ski combination is valid.
     *
     * @param array $resource of the ski information
     * @return bool true if it is a valid combination
     */
    public function isValidSki(array $resource): bool
    {
        $query = "SELECT COUNT(*) FROM `skitype` WHERE type = :type AND model = :model AND temperature = :temp AND gripSystem = :grip";
        $stmt = $this->db->prepare($query);
        for ($i = 0; $i < count($resource); $i++){
            $stmt->bindValue(':type', $resource[$i]["type"]);
            $stmt->bindValue(':model', $resource[$i]["model"]);
            $stmt->bindValue(':temp', $resource[$i]["temperature"]);
            $stmt->bindValue(':grip', $resource[$i]["gripSystem"]);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_NUM);
            if ($row == 0){
                return false;
            }
        }
        return true;
    }
}