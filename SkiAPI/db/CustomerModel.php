<?php

require_once 'SkiAPI/db/DB.php';

class CustomerModel extends db
{


    /**
     * Function to get a costumer.
     *
     * @param int $id of the customer we want to get
     * @return array with the information of a customer.
     */
    function getResource(int $id): array
    {
        $res = array();
        $query = "SELECT `customerID`, `Cname`, `startDate`, `endDate`, `shippingAddress` FROM `customer` WHERE customerID = :customerID";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':customerID', $id);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = array(
                'customerID' => $row['customerID'],
                'Name' => $row['Cname'],
                'Address' => $this->decrypt($row['shippingAddress']),
                'startDate' => $row['startDate'],
                'endDate' => $row['endDate']
            );
        }
        return $res;
    }


    /**
     * Function to create a new customer.
     *
     * @param array $resource the information of a customer
     * @param array $queries the type of a customer (store, franchise or skier)
     * @return array the created customer
     */
    function createResource(array $resource, array $queries): array
    {
        $this->db->beginTransaction();
        $customerID = rand(0, 1000);
        $query = "INSERT INTO `customer`(`customerID`, `Cname`, `startDate`, `endDate`, `shippingAddress`)
            VALUES ( :customerID, :name ,:startDate ,:endDate, :address)";
        $hashedAddress = $this->hashInfo($resource['address']);
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':customerID', $customerID);
        $stmt->bindValue(':startDate', $resource['startDate']);
        $stmt->bindValue(':endDate', $resource['endDate']);
        $stmt->bindValue(':name', $resource['name']);
        $stmt->bindValue(':address', $hashedAddress);
        $stmt->execute();

        if (($queries['customer']) == "store"){
            $query = "INSERT INTO `franchise`(`customerID`, `negotiatedPrice`, `information`) 
                        VALUES (:customerID , :price ,':info')";
            $stmt1 = $this->db->prepare($query);
            $stmt1->bindValue(':customerID', $customerID);
            $stmt1->bindValue(':price', $resource['price']);
            $stmt1->bindValue(':info', $resource['info']);
            $stmt1 ->execute();

        }elseif (($queries['customer']) == "franchise"){
            $query = "INSERT INTO `store`(`customerID`, `negotiatedPrice`) 
                        VALUES (:customerID,:price)";
            $stmt1 = $this->db->prepare($query);
            $stmt1->bindValue(':customerID', $customerID);
            $stmt1->bindValue(':price', $resource['price']);
            $stmt1 ->execute();

        }elseif (($queries['customer']) == "teamskier"){

            $query = "INSERT INTO `teamskier`(`customerID`, `dateOfBirth`, `club`, `numberOfSkisYearly`) 
            VALUES (:customerID,':dateOfBirth',':club', :numberOfSkis)";
            $stmt1 = $this->db->prepare($query);
            $stmt1->bindValue(':customerID', $customerID);
            $stmt1->bindValue(':dateOfBirth', $resource['dateOfBirth']);
            $stmt1->bindValue(':club', $resource['club']);
            $stmt1->bindValue(':numberOfSkis', $resource['numberOfSkis']);
            $stmt1 ->execute();
        }

        $this->db->commit();
        return $this->getResource($customerID);
    }


    /**
     * Function to encode a string
     *
     * Function is taken from:
     *      https://www.geeksforgeeks.org/how-to-encrypt-and-decrypt-a-php-string/
     *
     * @param string $info we would like to encode
     * @return string of the encoded string
     */
    function hashInfo(string $info):string {

        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';

        // Store the encryption key
        $encryption_key = "prosjektErGøy";

        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($info, $ciphering,
            $encryption_key, $options, $encryption_iv);

        return $encryption;
    }


    /**
     * Function to decode the encoded string
     *
     * Function is taken from:
     *      https://www.geeksforgeeks.org/how-to-encrypt-and-decrypt-a-php-string/
     *
     * @param string $info we would like to decode
     * @return string of the decode string
     */
    function decrypt(string $info): string {
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';

        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Store the decryption key
        $decryption_key = "prosjektErGøy";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Use openssl_decrypt() function to decrypt the data
        $decryption=openssl_decrypt ($info, $ciphering,
            $decryption_key, $options, $decryption_iv);

        return $decryption;
    }


}