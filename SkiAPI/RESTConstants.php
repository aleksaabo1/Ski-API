<?php

/**
 * Class RESTConstants class for application constants.
 */
class RESTConstants
{

    const API_URI = 'http://localhost:443';


    // HTTP method names
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    // HTTP status codes
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NO_CONTENT = 204;
    const HTTP_Not_Acceptable = 406;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_AUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;


    //usersEndpoint
    const ENDPOINT_CUSTOMERUSER = 'customer';
    const ENDPOINT_CUSTOMERREP = 'rep';
    const ENDPOINT_STOREKEEPER = 'storekeeper';
    const ENDPOINT_PRODUCTIONPLANNER = 'productionplan';
    const ENDPOINT_TRANSPORTER = 'transporter';
    const ENDPOINT_PUBLIC = 'public';



    // Defined application endpoints
    const ENDPOINT_ORDERS = 'orders';
    const ENDPOINT_ORDER = 'order';
    const ENDPOINT_SKI = 'ski';
    const ENDPOINT_CUSTOMER = 'employee';
    const ENDPOINT_SHIPMENT = 'shipment';
    const ENDPOINT_SPLIT = 'split';



    // Defined database errors
    const DB_ERR_ATTRIBUTE_MISSING = 1;
    const DB_ERR_FK_INTEGRITY = 2;

    // Defined foreign key violations
    const DB_FK_DEALER_COUNTY = 1001;
    const DB_FK_CAR_DEALER = 1002;
}
