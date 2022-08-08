<?php


use JetBrains\PhpStorm\Pure;

class Validation extends db
{


    /**
     * Function that will check if the user inputs are valid, when creating an order.
     *
     * @param array $data is the body the user is passing in, with the description of the order.
     * @return bool true if it is a valid order.
     */
    public function orderValidation(array $data): bool
    {

        $length = array('142','147','152','157','162','167','172','177','182','187','192','197','202','207');
        $weight = array('20-30','30-40','40-50','50-60','60-70','70-80','80-90','90+');
        $model = array('active','activePro','endurance','intrasonic','racePro','raceSpeed','redline');
        $type = array('classic','skate','doublePole');
        $grip = array('wax','intelliGrip');
        $temp = array('cold','warm','regular');

        for ($j = 0; $j < count($data); $j++) {
            if (!isset($data[$j])){
                return false;
            }
            $Check = $this->isValidMatch($data[$j]);
            if (!$Check) {
                return false;
            }
            if (!isset($data[$j]['length']) || !isset($data[$j]['weight']) || !isset($data[$j]['model']) || !isset($data[$j]['type']) || !isset($data[$j]['gripSystem']) || !isset($data[$j]['temperature']))
            {
                print 2;
                return false;
            }
            $count = 0;
            for ($i = 0; $i < count($length); $i++){
                if ($data[$j]['length'] == $length[$i]){
                    $count++;
                }
            }

            for ($i = 0; $i < count($weight); $i++){
                if ($data[$j]['weight'] == $weight[$i]){
                    $count++;
                }
            }

            for ($i = 0; $i < count($model); $i++){
                if ($data[$j]['model'] == $model[$i]){
                    $count++;
                }
            }

            for ($i = 0; $i < count($type); $i++){
                if ($data[$j]['type'] == $type[$i]){
                    $count++;
                }
            }

            for ($i = 0; $i < count($grip); $i++){
                if ($data[$j]['gripSystem'] == $grip[$i]){
                    $count++;
                }
            }

            for ($i = 0; $i < count($temp); $i++){
                if ($data[$j]['temperature'] == $temp[$i]){
                    $count++;
                }
            }

            if ($count != 6){
                return false;
            }
        }

        return true;
    }


    /**
     * Function that will validate if the combinations in the order is a real ski type
     *
     *
     * @param array $resource the body the user has passed in.
     * @return bool true if the user passed in a valid ski.
     */
    function isValidMatch(array $resource): bool{
        if (!isset($resource['model']) || !isset($resource['type']) || !isset($resource['gripSystem']) || !isset($resource['temperature'])){
            return false;
        }
        $query = "SELECT COUNT(*) FROM `skitype` WHERE model = :model AND type = :type AND gripSystem = :grip AND temperature = :temperature";
        $stmt = $this -> db -> prepare($query);
        $stmt ->bindValue(':model', $resource['model']);
        $stmt ->bindValue(':type', $resource['type']);
        $stmt ->bindValue(':grip', $resource['gripSystem']);
        $stmt ->bindValue(':temperature', $resource['temperature']);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_NUM );
        if ($row == 0 ){
            return false;
        }
        return true;
    }

}