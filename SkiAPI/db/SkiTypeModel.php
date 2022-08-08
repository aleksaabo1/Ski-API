<?php


class SkiTypeModel extends db
{

    /**
     * A function that will return the skis from the database.
     * @param array|null $query is an alternative optional filter if the user want to look at skis with different models, or gripsystem.
     * @return array with the corresponding skis.
     */
    function getCollection(array $query = null): array
    {
        $this -> db ->beginTransaction();
        $res = array();
        if (isset($query['model'])){
            $querySelect = "SELECT `skiID`, `type`, `model`, `temperature`, `gripSystem`, `description`, `historical`, `url`, `retailPrice` 
                    FROM `skitype` WHERE model = :model";
            $stmt = $this->db->prepare($querySelect);
            $stmt->bindValue(':model', $query['model']);
            $stmt -> execute();

        }elseif (isset($query['grip'])){
            $querySelect = "SELECT `skiID`, `type`, `model`, `temperature`, `gripSystem`, `description`, `historical`, `url`, `retailPrice` 
                    FROM `skitype` WHERE gripSystem = :grip";
            $stmt = $this->db->prepare($querySelect);
            $stmt->bindValue(':grip', $query['grip']);
            $stmt -> execute();
        } else{
            $querySelect = 'SELECT `skiID`, `type`, `model`, `temperature`, `gripSystem`, `description`, `historical`, `url`, `retailPrice`  FROM skitype';
            $stmt = $this->db->query($querySelect);
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'Model' => $row['model'],
                'Type' => $row['type'],
                'Temperature' => $row['temperature'],
                'Grip System' => $row['gripSystem'],
                'Description' => $row['description'],
                'Historical' => $row['temperature'],
                'Retail Price' => $row['retailPrice']);
        }
        return $res;
    }


    /**
     * Function that will return the price of an order.
     *
     * @param int $OrderNumber is id of the order we want to calculate the price.
     * @return int is the price of the order.
     */
    public function calculatePrice(int $OrderNumber): int{
        $res = array();
        $price = 0;
        $query = "SELECT `skiID`, count(*) as NUM FROM ordercontent where orderNumber = :orderNumber
                    GROUP BY skiID";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':orderNumber', $OrderNumber);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'skiID' => $row['skiID'],
                'quantity' => $row['NUM']);
        }
        $getPrice = "SELECT `retailPrice` FROM `skitype` WHERE skiID = :id";
        $stmt1 = $this->db->prepare($getPrice);
        for ($i = 0; $i < count($res); $i++){
            $stmt1->bindValue(':id', $res[$i]["skiID"]);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_NUM);
            $price += $row[0] * $res[$i]["quantity"];
        }
        return $price;
    }
}