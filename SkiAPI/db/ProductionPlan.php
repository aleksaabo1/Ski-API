<?php


class ProductionPlan extends db
{

    /**
     * Function to display the production plans of skis.
     *
     * @param array|null $queries an filter if the user want to search for a plan after a given time.
     * @return array the plans.
     */
    function getCollection(array $queries = null): array
    {
        $res = array();
        if (isset($queries['date'])){
            $query = "SELECT `start_date`, `end_date`, `numberOfSki`, `employeeNumber`, `skiID` FROM `productionplan` WHERE start_date < :date";
            $stmt = $this->db->prepare($query);
            $stmt ->bindValue(':date', $queries['date']);
        }else{
            $query = 'SELECT `start_date`, `end_date`, `numberOfSki`, `employeeNumber`, `skiID` FROM `productionplan`';
            $stmt = $this->db->query($query);
        }
        $stmt ->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'Start Date' => $row['start_date'],
                'End date' => $row['end_date'],
                'skiID' => $row['skiID'],
                'Nr.' => $row['numberOfSki'],
                'Employee' => $row['employeeNumber']
            );
        }
        return $res;
    }


    /**
     * Function to display a specific  production plan.
     *
     * @param string $date of the created plan.
     * @return array the plans.
     */
    function getResource(string $date): array
    {
        $res = array();
        $query = "SELECT `start_date`, `end_date`, `numberOfSki`, `employeeNumber`, `skiID` FROM `productionplan` WHERE start_date = :date";
        $stmt = $this->db->prepare($query);
        $stmt ->bindValue(':date', $date);

        $stmt ->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'Start Date' => $row['start_date'],
                'End date' => $row['end_date'],
                'skiID' => $row['skiID'],
                'Nr.' => $row['numberOfSki'],
                'Employee' => $row['employeeNumber']
            );
        }
        return $res;
    }



    /**
     * Function that will create a new production plan for the different skitypes.
     *
     * @param array $resource the required fields in the
     * @return array|int the newly created plan if it was successful, or a status code, if it was not successful.
     */
    function createResource(array $resource): array|int
    {
        $res = array();
        $this->db->beginTransaction();
        $query = "INSERT INTO `productionplan`(`start_date`, `end_date` ,`employeeNumber`, `skiID`, `numberOfSki`) 
            VALUES (:start_date, DATE_ADD(:start_date, INTERVAL 30 DAY) ,:employee, :id, :number)";
        $stmt = $this->db->prepare($query);
        if (isset($resource['start_date']) && isset($resource['skiID']) && isset($resource['numberOfSki'])) {
            $stmt->bindValue(':start_date', $resource['start_date']);
            $stmt->bindValue(':employee', (new AuthorisationModel())->extractID($_COOKIE['auth_token']));
            $stmt->bindValue(':id', $resource['skiID']);
            $stmt->bindValue(':number', $resource['numberOfSki']);
            $stmt->execute();
            $this->db->commit();
            return $this->getResource($resource['start_date']);
        }else{
            return array();
        }
    }


}

