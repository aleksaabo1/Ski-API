# Ski Database Project IDATG2204
This project is inspired and uses code from Rune's "Sample REST API project": https://git.gvk.idi.ntnu.no/runehj/sample-rest-api-project

The project case: https://git.gvk.idi.ntnu.no/course/idatg2204/idatg2204-2021/-/wikis/The-Project-Case

### Project group
- Aleksander Aaboen
- Karin Emilie Pettersen
- Sander Island

## Endpoints

#### Customer Endpoint
GET requests:
```
/customer/order?{date}                      -Gets a list with the customer's orders, with since filter.
/customer/order/{orderNumber}               -Gets specific order.
/customer/plan                              -Gets a four-week production plan.
```
POST request:
```
/customer/order                             -Place an order.
```
 Body:

```
    [{
    "model": "active",
    "type": "skate",
    "temperature": "regular",
    "gripSystem": "wax",
    "length": "157",
    "weight": "90+"
    }]

```

PUT requests:
```
/customer/order/{orderNumber}               -Request to split an order if not all skis are ready.
```

DELETE request:
```
/customer/order/{orderNumber}               -Cancel an order.
```

#### Transporter Endpoint
GET request:
```
/transporter/order                          -Get orders that are "ready".
```
PUT request:
```
/transporter/order/{shipmentNumber}?setstate={state}   -Change state when an order is picked up.
```

#### Employee Endpoint
GET request:
```
/employee/order?state={state}               -Get order based on state.(Customer Representative)
/employee/order                             -Get available orders. (Storekeeper)
```
POST request:
```
/employee/shipment                          -Make a shipment request. (Customer Representative)
```
Body Example
```
  {     
      "transporterID": 1
  }
```


```
/employee/plan                              -Make a four-week production plan. (Production Planner)
```

Body example:

```
{
   "start_date": "2021-05-27",
   "skiID": 2,
   "numberOfSki": 10000
}
```


PUT request:
```
/employee/order/{orderNumber}?setstate={state}         -Change order state. (Customer Representative and Storekeeper)
```

#### Public Endpoint
GET request:
```
/ski?model={model}                          -Get skis based on model.
/ski?grip={gripSystem}                      -Get skis based on grip system.
```



## Setting up the project
### Requirements
1. XAMPP
2. PhpStorm

### Setting up the database
1. Open XAMPP
2. Start Apache and MySQL, then click the "Admin" button on MySQL. phpMyAdmin will now open.
3. Create a new database and import project.sql. 

### Run
1. Clone git repository: https://git.gvk.idi.ntnu.no/course/idatg2204/idatg2204-2021-workspace/aleksaab/database_project.git
2. Open the project in PhpStorm
3. Run the program on your selected port:
```
 http://localhost:{port}/{endpoint}
```
4. The different endpoints will require the use of cookies.


```
Name of cookie is auth_token and value is for:

Customer: qaz , followed by an integer between 1-7
Example: qaz3

Customer: asd , followed by an integer between 1-3
1 reperesent a storekeeper
2 reperesent a Production Planner
3 reperesent a Customer Representative

Customer: wsx , followed by an integer between 1-3
Example: wsx2

```

To run the endpoints you need to edit your run configurations. 
- Step 1: Add new configuration: PHP Built-in Web Server
- Step 2: Set preferred Host: and Port:
- Step 3: Check the "Use router script", and select the api.php file
- Step 4: Select the project folder as "Document root"
- Step 5: Download the physical Database, and import to phpMyAdmin
