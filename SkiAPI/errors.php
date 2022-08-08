<?php
require_once 'RESTConstants.php';

/**
 * Holding error messages to be returned to the client for the various types of HTTP and database error cases
 * @global array $ERROR_MESSAGES
 */
$ERROR_MESSAGES = array();

$ERROR_MESSAGES[RESTConstants::HTTP_BAD_REQUEST] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_BAD_REQUEST]['title'] = 'Bad Request';
$ERROR_MESSAGES[RESTConstants::HTTP_BAD_REQUEST]['detail'] = 'The request is badly formatted';

$ERROR_MESSAGES[RESTConstants::HTTP_FORBIDDEN] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_FORBIDDEN]['title'] = 'Forbidden';
$ERROR_MESSAGES[RESTConstants::HTTP_FORBIDDEN]['detail'] = 'The client is not authorized to perform the requested operation';

$ERROR_MESSAGES[RESTConstants::HTTP_NOT_FOUND] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_NOT_FOUND]['title'] = 'Not found';
$ERROR_MESSAGES[RESTConstants::HTTP_NOT_FOUND]['detail'] = 'The requested resource does not exist';

$ERROR_MESSAGES[RESTConstants::HTTP_METHOD_NOT_ALLOWED] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_METHOD_NOT_ALLOWED]['title'] = 'Method not allowed';
$ERROR_MESSAGES[RESTConstants::HTTP_METHOD_NOT_ALLOWED]['detail'] = 'The requested operation is not allowed on this resource';

$ERROR_MESSAGES[RESTConstants::HTTP_INTERNAL_SERVER_ERROR] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_INTERNAL_SERVER_ERROR]['title'] = 'Internal system error';
$ERROR_MESSAGES[RESTConstants::HTTP_INTERNAL_SERVER_ERROR]['detail'] = 'The system failed of unknown reason';

$ERROR_MESSAGES[RESTConstants::HTTP_NOT_IMPLEMENTED] = array();
$ERROR_MESSAGES[RESTConstants::HTTP_NOT_IMPLEMENTED]['title'] = 'Not implemented';
$ERROR_MESSAGES[RESTConstants::HTTP_NOT_IMPLEMENTED]['detail'] = 'The requested operation is not implemented for this resource';

$ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING] = array();
$ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING]['title'] = 'Problems with user data';
$ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING]['detail'] = 'Attribute(s) is (are) missing in the resource representation';

$ERROR_MESSAGES[RESTConstants::DB_ERR_FK_INTEGRITY] = array();
$ERROR_MESSAGES[RESTConstants::DB_ERR_FK_INTEGRITY]['detail'] = array();
$ERROR_MESSAGES[RESTConstants::DB_ERR_FK_INTEGRITY]['detail'][RESTConstants::DB_FK_DEALER_COUNTY] = 'No matching county resource found';
$ERROR_MESSAGES[RESTConstants::DB_ERR_FK_INTEGRITY]['detail'][RESTConstants::DB_FK_CAR_DEALER] = 'No matching dealer resource found';

$ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING] = array();
$ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING]['detail'] = 'Attribute(s) is (are) missing in the resource representation';

/**
 * Generates an array holding the information to be passed to the client.
 * @param int $error_code the HTTP error code causing the error
 * @param string $instance the URI of the resource detecting the error
 * @param int $detailCode the code of the specific type of error
 * @param Throwable $e the exception that caused the error - if applicable
 * @return array an array of the form array("error-code": nn, "title": "...", "detail": "...", "instance": "...")
 * @global array ERROR_MESSAGES
 */
function generateErrorResponseContent(int $error_code, string $instance, int $detailCode = -1, Throwable $e = null): array {
    $res = array();
    global $ERROR_MESSAGES;

    $res['error_code'] = $error_code;
    $res['title'] = $ERROR_MESSAGES[$error_code]['title'];
    $res['detail'] = $ERROR_MESSAGES[$error_code]['detail'];
    $res['instance'] = $instance;

    return $res;
}

/**
 * Generates an array holding the information to be passed to the client in the case of an bad user request.
 * @param int $error_code the HTTP error code causing the error
 * @param string $instance the URI of the resource detecting the error
 * @param int $detailCode the code of the specific type of error
 * @return array an array of the form array("error-code": nn, "title": "...", "detail": "...", "instance": "...")
 * @global array ERROR_MESSAGES
 */
function generateDBErrorResponseContent(int $error_code, string $instance, int $detailCode): array
{
    $res = array();
    global $ERROR_MESSAGES;

    $res['error_code'] = RESTConstants::HTTP_BAD_REQUEST;
    $res['title'] = $ERROR_MESSAGES[RESTConstants::HTTP_BAD_REQUEST]['title'];
    if ($error_code == RESTConstants::DB_ERR_ATTRIBUTE_MISSING) {
        $res['detail'] = $ERROR_MESSAGES[RESTConstants::DB_ERR_ATTRIBUTE_MISSING]['detail'];
    } else if ($error_code == RESTConstants::DB_ERR_FK_INTEGRITY) {
        $res['detail'] = $ERROR_MESSAGES[RESTConstants::DB_ERR_FK_INTEGRITY]['detail'][$detailCode];
    }
    $res['instance'] = $instance;

    return $res;
}
