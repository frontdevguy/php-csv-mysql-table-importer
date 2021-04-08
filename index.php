<?php
use \CSVReader\CSVReader;
use App\DBConnection;

include __DIR__ . '/CSVReader.php';
include __DIR__ . '/DBConnection.php';

const DB_NAME = 'orderTest';
const COLUMN_SEPARATOR = '|';
const ROWS_DELIMITER = ',';
const TABLE_NAME = 'orders';
const TABLE_COLUMNS = ['email', 'name', 'address_1', 'address_2', 'country', 'phone_number'];
const CSV_FILE_PATH = __DIR__ . '/orders.csv';
const SCHEMA_FILE = __DIR__ . '/schema.sql';

// Change this function as needed
function getValuesFromRowData($rowData) {
    $object = new stdClass();
    $object->isValid = true; // can do validation
    $rowDataLength = count($rowData);
    $email = $rowData[0] ?? '';
    $name = $rowData[1] ?? '';
    $address1 = $rowData[2] ?? '';
    $address2 = implode(" ",array_slice($rowData,3, $rowDataLength - 5));
    $country = $rowData[$rowDataLength - 2];
    $phoneNumber = $rowData[$rowDataLength - 1];
    $object->values = [$email, $name, $address1, $address2, $country, $phoneNumber];
    return $object;
}

function runSchema($connection) {
    $sql = file_get_contents(SCHEMA_FILE);  
    $stmt = $connection->prepare($sql);
    return $stmt->execute();
}

function runImporter($connection, $cb, $printBadData = true) {
    $ordersCSVReader = new CSVReader(CSV_FILE_PATH, ROWS_DELIMITER, false);
    $stmt = $connection->prepare("INSERT INTO ".TABLE_NAME."(".implode(',', TABLE_COLUMNS).") VALUES (".implode(",",array_fill(0,count(TABLE_COLUMNS),"?")).")");
    $badData = [];
    foreach($ordersCSVReader->csvToArray() as $data){
        foreach($data as $sub_data) {
            if(is_array($sub_data)) {
                $orderData = (explode(COLUMN_SEPARATOR,array_values($sub_data)[0]));
                $resource = $cb($orderData);
                $resource->isValid === true ? $stmt->execute($resource->values) : array_push($badData, $resource->values);
            }
        }
    }
    echo 'Operation completed!';
    if($printBadData === true && count($badData) > 0) {
        echo '<br/><pre><br/>Bad Data Input Below <br />';
        foreach($badData as $data) print_r($data);
    }
}

try {
    $dbInstance = new DBConnection();
    $connection = $dbInstance->openConnection(DB_NAME);
    runSchema($connection);
    runImporter($connection, 'getValuesFromRowData');
    $dbInstance->closeConnection();
} catch (\Exception $e) {
    echo '<pre>';
    print_r($e);
    die($e->getMessage());
}