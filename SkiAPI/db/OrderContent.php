<?php


class OrderContent extends db
{


    /**
     * Function that will return the content of an order.
     *
     * @param int $orderNumber of the order we want to get the content.
     * @return array of the the content in the order.
     */
    public function getContent(int $orderNumber): array
    {
        $res = array();
        $query = "SELECT `ID`, `orderNumber`, `skiID`, `length`, `weight` FROM `ordercontent` WHERE orderNumber = :orderNumber";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':orderNumber', $orderNumber);
        $stmt -> execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'orderNumber' => $row['orderNumber'],
                'skiID' => $row['skiID'],
                'length' => $row['length'],
                'weight' => $row['weight'],
            );
        }

        return $res;


    }


    /**
     * Function that will assign skis to an order. The skis is not required to exist in the database.
     *
     * @param array $resource the required fields that a user must insert to make an order
     * Such as type, model, temperature, and grip system.
     * @param int $orderNumber the order we want to assign the skis.
     */
    public function addContent(array $resource, int $orderNumber){
       if (!$this ->db ->inTransaction()){
           $this -> db -> beginTransaction();
       }
        $id = rand(1000, 999999);
        $getSkis = "SELECT skiID, retailPrice  FROM `skitype`
                WHERE `type` = :skiType AND `model` = :skiModel AND 
                      `temperature` = :skiTemp AND gripSystem = :skiGrip";

        $stmtSki = $this->db->prepare($getSkis);
        for ($i = 0; $i < count($resource); $i++) {
            $stmtSki->bindValue(':skiType', $resource[$i]['type']);
            $stmtSki->bindValue(':skiModel', $resource[$i]['model']);
            $stmtSki->bindValue(':skiTemp', $resource[$i]['temperature']);
            $stmtSki->bindValue(':skiGrip', $resource[$i]['gripSystem']);
            $stmtSki->execute();
            while ($row = $stmtSki->fetch(PDO::FETCH_ASSOC)) {
                $queryOrder = "INSERT INTO `orderContent`(`ID`, `orderNumber`, `skiID`, `weight`, `length`) 
                            VALUES (:id , :orderNr, :skiID, :weight, :skiLength)";
                $stmt = $this->db->prepare($queryOrder);
                $skiID = $row['skiID'];
                $stmt->bindValue(':id', $id + $i);
                $stmt->bindValue(':orderNr', $orderNumber);
                $stmt->bindValue(':skiID', $skiID);
                $stmt->bindValue(':weight', $resource[$i]['weight']);
                $stmt->bindValue(':skiLength', $resource[$i]['length']);
                $stmt->execute();
            }
        }
        $this -> db -> commit();
    }




}