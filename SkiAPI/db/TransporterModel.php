<?php


class TransporterModel extends db
{

    /**
     * @param string $id the transporterID of the the driver
     * @return bool true if there exist a driver with the corresponding id
     *              false if there is no transporter with this id
     */
    function validDriverID(string $id):bool{
        $query = "SELECT count(*) FROM `transporter` WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $row = $stmt -> fetch(PDO::FETCH_NUM);
        if ($row == 0){
            return false;
        }
        return true;
    }




}