<?php

require_once 'handleOrder.php';
require_once 'shipmentRecording.php';
require_once 'SkiAPI/db/TransporterModel.php';



class ShipmentModel extends db
{

    /**
     * Function that will give you information about a shipment.
     *
     * @param int $id of the shipment
     * @return array|null the details of the shipment
     */
    function getResource(int $id): ?array
    {
        $res = array();
        $query = "SELECT `shipmentNumber`, `customerID`, `scheduledPickupDate`, `state`, `transporterID`, `orderNumber` FROM `shipment`
                    WHERE shipmentNumber = :id";
        $stmt = $this -> db ->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt -> execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $res[] = array('Shipment Number' => intval($row['shipmentNumber']),
                'Customer' => $row['customerID'],
                'Pick Up Date' => $row['scheduledPickupDate'],
                'Status' => $row['state'],
                'Transporter ID' => $row['transporterID'],
                'Order Number' => $row['orderNumber']);
        }
        return $res;
    }


    /**
     * Function to create a shipment request for an order.
     *
     *
     * @param array $resource is the information required for the shipment
     * @param int $orderNumber is the id of the order we want to ship
     * @return array|null returns the shipment request
     */
    function createResource(array $resource, int $orderNumber ): ?array
    {

        if ($orderNumber == 0){
            return array();
        }

        $order = new OrderModel();
        $transporter = new TransporterModel();
        $res = array();
        if ($order -> isReadyForShipping($orderNumber)){
            print "Order is not ready for shipment";
            return null;
        }
        $getCustomer = "SELECT customer.customerID, `Cname`, `startDate`, `endDate`, `shippingAddress` FROM `customer` 
        left outer join orders on orders.customerID = customer.customerID 
        where orders.orderNumber =:orderNr";
            $stmt = $this->db->prepare($getCustomer);
            $stmt->bindValue(':orderNr', $orderNumber);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res = array(
                    'customerID' => intval($row['customerID']),
                    'Cname' => $row['Cname'],
                    'startDate' => $row['startDate'],
                    'endDate' => $row['endDate'],
                    'shippingAddress' => $row['shippingAddress']);
            }

        if ($transporter ->validDriverID($resource['transporterID'])){
            print "DriverID is not valid";
            return null;
        }

        $this->db->beginTransaction();
        $query = "INSERT INTO `shipment`(`shipmentNumber`, `orderNumber`, `transporterID`, `customerID`, `state`)
        VALUES (:shipmentNumber,:orderNumber,:transporterID,:customerID,:state)";
        $stmt1 = $this->db->prepare($query);
        $shipmentNumber = rand(0, 999999);
        print $resource['transporterID'];
        $stmt1->bindValue(':shipmentNumber', $shipmentNumber);
        $stmt1->bindValue(':customerID', $res['customerID']);
        $stmt1->bindValue(':transporterID', $resource['transporterID']);
        $stmt1->bindValue(':orderNumber', $orderNumber);
        $stmt1->bindValue(':state', 'ready');
        $stmt1->execute();
        $this->db->commit();
        return $this->getResource($shipmentNumber);
    }


    /**
     * Function to update a shipment state.
     *
     * @param int $shipmentNumber we want to update.
     * @param array $resource is the state we want to change the shipment.
     * @return array the updated shimpent.
     */
    function updateResource(int $shipmentNumber, array $resource): array
    {
        $this->db->beginTransaction();
        $res = array();
        $query = "UPDATE `shipment` SET `state` = :status WHERE shipmentNumber = :shipmentNumber";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':shipmentNumber', $shipmentNumber);
        $stmt->bindValue(':status', $resource['setstate']);
        $stmt->execute();
        $this->db->commit();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $address = (new CustomerModel()) ->decrypt($row['shippingAddress']);
            $res[] = array('shipmentNumber' => intval($row['shipmentNumber']),
                'orderNumber' => $row['orderNumber'],
                'driverId' => $row['driverID'],
                'customer' => $row['customer'],
                'shippingAddress' => $address,
                'scheduledPickUpDate' => $row['scheduledPickUpDate'],
                'state' => $row['state']);
        }
        return $this->getResource($shipmentNumber);
    }
}