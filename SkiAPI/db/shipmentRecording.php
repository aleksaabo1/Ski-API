<?php


class shipmentRecording extends db
{

    /**
     * A Function to record a transition with the shipment.
     *
     * @param int $shipmentNumber of the shipment that has been handled.
     * @param int $driver is the driver that is handling the shipment.
     * @param string $comment about the job.
     */
    function addTransition(int $shipmentNumber, int $driver, string $comment){
        $this->db->beginTransaction();
        $query = "INSERT INTO `shipmentrecordings`(`transporterID`, `shipmentNumber`, `comment`) 
        VALUES (:transporterID, :shipmentNumber, :comment)";
        $stmt = $this->db->prepare($query);
        $stmt -> bindValue(':transporterID', $driver);
        $stmt -> bindValue(':shipmentNumber', $shipmentNumber);
        $stmt -> bindValue(':comment', $comment);
        $stmt -> execute();
        $this ->db->commit();
    }


}





