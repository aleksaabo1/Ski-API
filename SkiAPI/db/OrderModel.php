<?php
require_once 'DB.php';
require_once 'AbstractModel.php';
require_once 'SkiModel.php';
require_once 'OrderContent.php';

require_once 'SkiAPI/controller/Validation.php';



/**
 * Class OrderModel class for accessing order data in database.
 */
class OrderModel extends db
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Function getOrdersByState, retrieve a list of orders they have made
     * @param array $queries the state of the order, which we would like to receive.
     * @return array an array of associative arrays holding orderNumber, quantity, totalPrice, state and date
     */
    function getOrdersByState(array $queries): array
    {
        $res = array();
        $query = "SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` FROM orders WHERE state = :state";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':state', $queries['state']);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array('orderNumber' => ($row['orderNumber']),
                'quantity' => $row['quantity'],
                'totalPrice' => $row['totalPrice'],
                'state' => $row['state'],
                'date' => $row['date']);
        }
        if ($res == null){
            print "No orders";
        }
        return $res;
    }


    /**
     */

    /**
     * Function getAllOrders, retrieve all of the authorized customers order.
     *
     * @param array|null $filter is an optional filter if the customer wanted to filter the results with dates
     * @return array|null an array of associative arrays holding orderNumber, quantity, totalPrice, state and date
     */
    function getCollectionCustomer(array $filter = null): ?array
    {
        $res = array();
        $auth = new AuthorisationModel();
        $customer = $auth->extractID($_COOKIE['auth_token']);
        print $customer;
        if (isset($filter['date'])){
            $query = 'SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` 
            FROM orders where customerID = :id AND date > :date';
            $stmt = $this->db->prepare($query);
            $stmt -> bindValue(':id', $customer);
            $stmt -> bindValue(':date', $filter['date']);
        }else{
            $query = 'SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` FROM orders where customerID = :id';
            $stmt = $this->db->prepare($query);
            $stmt -> bindValue(':id', $customer);
        }
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array('orderNumber' => intval($row['orderNumber']),
                'quantity' => $row['quantity'],
                'totalPrice' => $row['totalPrice'],
                'state' => $row['state'],
                'date' => $row['date']);
        }
        return $res;
    }


    /**
     *
     * Function that will return all the orders in the system.
     *
     * @param array|null $filter an optional filter that can filter the orders with dates.
     * @return array|null an array of associative arrays holding orderNumber, quantity, totalPrice, state and date
     */
    function getCollection(array $filter = null): ?array
    {
        $res = array();
        if (isset($filter['state'])){
            $query = 'SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` 
            FROM orders where state = :state';
            $stmt = $this->db->prepare($query);
            $stmt -> bindValue(':state', $filter['state']);

        }else{
            $query = 'SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` FROM orders';
            $stmt = $this->db->query($query);
        }
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array('orderNumber' => intval($row['orderNumber']),
                'quantity' => $row['quantity'],
                'totalPrice' => $row['totalPrice'],
                'state' => $row['state'],
                'date' => $row['date']);
        }

        return $res;
    }



    /**
     * Function getResource, retrieve information about a specific order and its state.
     * @param int $id, the orderNumber the user is sending
     * @return array an array of associative arrays holding orderNumber, quantity, totalPrice, state and date
     */
    function getResource(int $id): array
    {
        $res = array();
        if ($this -> isAValidOrder($id)){
            $query = 'SELECT `orderNumber`, `quantity`, `totalPrice`, `state`, `date` FROM orders WHERE orderNumber = :orderNumber';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':orderNumber', $id);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[] = array('orderNumber' => intval($row['orderNumber']),
                    'quantity' => $row['quantity'],
                    'totalPrice' => $row['totalPrice'],
                    'state' => $row['state'],
                    'date' => $row['date']);
            }
            return $res;
        }else{
            return array();
        }
    }


    /**
     * Function deleteOrder, cancel an order.
     * @param int $id , sends in the orderNumber the user wants to cancel
     * @return array an array of associative arrays holding orderNumber, quantity, totalPrice, state and date
     */
    function cancelResource(int $id): array
    {
        $auth = new AuthorisationModel();
        $valid = $auth -> isYourOrder($id);
        if ($valid){
            $this -> db -> beginTransaction();
            $queries['setstate'] = "cancelled";
            $this->updateOrderState($id, $queries);
            $query1 ="UPDATE `ski` SET `reservedToOrder`= null WHERE reservedToOrder = :orderNumber";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bindValue(':orderNumber', $id);
            $stmt1->execute();
            return $this->getResource($id);
        }else {
            return array();
        }
    }


    /**
     * Function that will create an empty order.
     * This due to foreign keys constrains.
     * @param int $orderNumber that is the empty order we want to create.
     */
    function addEmptyOrder(int $orderNumber){
        if (!$this->db->inTransaction()){
            $this->db->beginTransaction();
        }
        $query = "INSERT INTO `orders`(`orderNumber`)
                VALUES (:orderNumber)";
        $stmt = $this -> db -> prepare($query);
        $stmt->bindValue(':orderNumber', $orderNumber);
        $stmt -> execute();
        $this -> db -> commit();
    }





    /**
     * Creates a new resource in the database.
     * @param array $resource the resource to be created.
     * @return array an associative array of resource attributes representing
     *               the resource - the returned value will include the id
     *               assigned to the resource.
     *               the database
     */
    function createResource(array $resource): array
    {
        $ski = new SkiTypeModel();
        $skiModel = new SkiModel();

        $auth = new AuthorisationModel();
        $content = new OrderContent();
        $valid = new Validation();

        if ($valid -> orderValidation($resource) && $skiModel -> isValidSki($resource)) {
            $orderNr = rand(1000, 999999);
            $this -> addEmptyOrder($orderNr);
            $content->addContent($resource, $orderNr);
            $orderContent = $content->getContent($orderNr);
            $priceTotal = $ski->calculatePrice($orderNr);

            if (!$this->db->inTransaction()){
                $this->db->beginTransaction();
            }
            $skiID = 0;

            for ($i = 0; $i < count($orderContent); $i++) {
                $skiReserved = "UPDATE `ski` SET `reservedToOrder`= :orderNumber WHERE skiID = :id AND length = :length
                        AND weight = :weight And reservedToOrder is null";
                $stmt2 = $this->db->prepare($skiReserved);
                $stmt2->bindValue(':orderNumber', $orderNr);
                $stmt2->bindValue(':id', $skiID);
                $stmt2->bindValue(':length', $orderContent[$i]['length']);
                $stmt2->bindValue(':weight', $orderContent[$i]['weight']);
                $stmt2->execute();
            }

            $query2 = 'UPDATE `orders` SET `quantity`= (SELECT COUNT(*) FROM orderContent where orderNumber = :orderNumber),
                        `totalPrice`= :totalPrice,`state`="new",`customerID`=:customerID 
                         WHERE `orderNumber`= :orderNumber';

            $stmtOrder = $this->db->prepare($query2);
            $stmtOrder->bindValue(':orderNumber', $orderNr);
            $stmtOrder->bindValue(':customerID', $auth->extractID($_COOKIE['auth_token']));
            $stmtOrder->bindValue(':totalPrice', $priceTotal);
            $stmtOrder->execute();

            $this->db->commit();
            return $this->getResource($orderNr);
        }else{
            return array();
        }
    }


    /**
     * Function that is updating the state of a given order.
     *
     * @param int $orderNr the order we want to update
     * @param array $queries the state we want to update the order to
     * @return array the updated order.
     */
    function updateOrderState(int $orderNr, array $queries): array
    {
        if (!$this->db->inTransaction()){
            $this ->db->beginTransaction();
        }
        if ($queries["setstate"] == "new" || $queries["setstate"] == "open" || $queries["setstate"] == "available" || $queries["setstate"] == "cancelled" || $queries["setstate"] == "ready") {
            $update = "UPDATE orders SET state = :state WHERE orderNumber=:orderNumber";
            $stmt = $this->db->prepare($update);
            $stmt->bindValue(':orderNumber', $orderNr);
            $stmt->bindValue(':state', $queries["setstate"]);
            $stmt->execute();
            $this->db->commit();
            return $this->getResource($orderNr);
        }else {
            return array();
        }
    }


    /**
     * Function to assign skis to a given order
     *
     * @param int $orderNumber the order we want to assign the given ski to
     * @param int $productID the ski we want to reserve to a given order
     * @return array the order that the ski was assigned to
     */
   function assigningSkis(int $orderNumber, int $productID):array{

       if (!$this ->db ->inTransaction()){
           $this ->db->beginTransaction();
       }

        $assign = "UPDATE `ski` SET `reservedToOrder`= :order WHERE productID = :productID";
       $stmt = $this->db->prepare($assign);
       $stmt->bindValue(':order', $orderNumber );
       $stmt->bindValue(':productID', $productID);
       $stmt ->execute();

       (new handleOrder()) -> addTransition($orderNumber, "Assigned ski: " . $productID . " to order");
       $this->db->commit();

       return $this -> getResource($orderNumber);
   }


    /**
     * Function that will split a order, if the order is partially filled with skis.
     * The order will be split into two orders. Where one is filled with skis that is assigned
     * and the other where skis are not assigned to an order yet.
     *
     * @param int $orderNumber the order we want to split.
     * @return array with the new orders.
     */
    public function splitOrder(int $orderNumber): array{

        $skimodel = new SkiModel();
        $Skis = $skimodel -> reserved($orderNumber);
        $getOrderContent = "SELECT count(*), ID, orderNumber, skiID, length, weight FROM ordercontent WHERE orderNumber = :orderNr GROUP BY skiID, length, weight";
        $stmt = $this->db->prepare($getOrderContent);
        $stmt -> bindValue(':orderNr', $orderNumber);
        $stmt -> execute();

        $OrderContent = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $OrderContent[] = array(
                'numberOfSkis' => $row['count(*)'],
                'id' => $row['ID'],
                'orderNumber' => $row['orderNumber'],
                'skiID' => $row['skiID'],
                'length' => $row['length'],
                'weight' => $row['weight']);
        }
        $newOrder = array();
        $orderContentID = array();
        if ($Skis != null && $OrderContent != null) {
            for ($i = 0; $i < count($OrderContent); $i++) {
                for ($j = 0; $j < count($Skis); $j++) {
                    if ($Skis[$j]['skiID'] == $OrderContent[$i]['skiID'] && $Skis[$j]['length'] == $OrderContent[$i]['length'] && $Skis[$j]['weight'] == $OrderContent[$i]['weight']) {
                        $getInfo = "SELECT `skiID`, `type`, `model`, `temperature`, `gripSystem` FROM `skitype` WHERE skiID = :id";
                        array_push($orderContentID, $OrderContent[$i]['id']);
                        $stmt1 = $this->db->prepare($getInfo);
                        $stmt1->bindValue(':id', $Skis[$j]['skiID']);
                        $stmt1->execute();
                        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                            $newOrder[] = array(
                                'type' => $row['type'],
                                'model' => $row['model'],
                                'temperature' => $row['temperature'],
                                'gripSystem' => $row['gripSystem'],
                                'length' => $Skis[$j]['length'],
                                'weight' => $Skis[$j]['weight']);
                        }
                    }
                }
            }
            if ($newOrder != null){
                for ($k = 0; $k < count($orderContentID); $k++) {
                    $this->deleteOrderContent($orderContentID[$k]);
                }
                $new = $this->createResource($newOrder);
               $queries["setstate"] = "available";
               $this->updateOrderState($new[0]["orderNumber"], $queries);
               $this->updateOrder($orderNumber);
            }
        }else {
            return array();
        }



        return array_merge($this->getResource($orderNumber), $this -> getResource($new[0]["orderNumber"])) ;

    }


    /**
     * Function to delete a ski in an order.
     *
     * @param int $id of the content order
     */
    function deleteOrderContent(int $id){
        $query = "DELETE FROM `ordercontent` WHERE ID = :id";
        $stmt = $this->db->prepare($query);
        $stmt -> bindValue(':id', $id);
        $stmt -> execute();
    }


    /**
     *
     * Function used in split order function. This function will get remaining skis in the order, and calculate the
     * new quantity and price of the order
     *
     * @param int $orderId you would like to update the order
     */
     function updateOrder(int $orderId)
     {
         if (!$this -> db -> inTransaction()){
             $this ->db-> beginTransaction();

         }
         $getSkis = "SELECT `skiID`, `length`, `weight` FROM `ordercontent` WHERE orderNumber = :orderID";
         $stmt = $this->db->prepare($getSkis);
         $stmt->bindValue(':orderID', $orderId);
         $stmt->execute();

         $orderContent = array();
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $orderContent[] = array(
                 'skiID' => $row['skiID'],
                 'length' => $row['length'],
                 'weight' => $row['weight'],
             );
         }

         $STRING = "";
         if ($orderContent != null) {
             for ($i = 0; $i < count($orderContent); $i++) {
                 $STRING .= ", " . $orderContent[$i]['skiID'];
             }
             $STRING = ltrim($STRING, ",");
         }

         $query2 = "UPDATE `orders` SET `quantity`= (SELECT COUNT(*) FROM orderContent where orderNumber = :orderNumber),
                    `totalPrice`=(SELECT SUM(retailPrice) FROM `skitype` WHERE skiID in (:skiIDs)),
                    `state`='new' WHERE orderNumber = :orderNumber";
         $stmtOrder = $this->db->prepare($query2);
         $stmtOrder->bindValue(':orderNumber', $orderId);
         $stmtOrder->bindValue(':skiIDs', $STRING);
         $stmtOrder->execute();
         $this->db->commit();
     }


    /**
     *
     * Function that will check if a given order number is a valid order number.
     * @param int $orderNumber the order we want to check exist.
     * @return bool true if the order is valid.
     */
     public function isAValidOrder(int $orderNumber): bool{
        $query = "SELECT count(*) FROM `orders` WHERE orderNumber = :ordernumber";
         $stmt = $this->db->prepare($query);
         $stmt->bindValue(':ordernumber', $orderNumber);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         if ($row >= 1){
             return true;
         }
         return false;
     }


    /**
     * Function that will check if the order is in the correct state, and ready for shipment.
     *
     * @param int $orderID we want to check is ready
     * @return bool true if order is ready for shipment.
     */
     public function isReadyForShipping(int $orderID): bool{
        $query = "SELECT `state` FROM `orders` WHERE orderNumber = :orderNumber";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':orderNumber', $orderID);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row == "available"){
            return true;
        }
        return false;
     }

}