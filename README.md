# Canoe Tech Assessment @ Remotely

This is an assessment for a job opportunity at Canoe, emphasizing the creation of a data model and a backend service.
My solution, developed using ```Laravel (PHP)```, ```VueJS (JavaScript)```, and ```MySQL```, allows fund managers to manually view and update fund records.
Additionally, I addressed the challenge of handling duplicate records.
Plus, I added a simple frontend to make it look good.

**Requirements:**
*```PHP >= 8.1```  is required, and you need to have a created ```database```. Also, dont forget to update your ```composer```.*

## Table of Contents

- [Overview](#markdown-header-overview)
- [Installation](#markdown-header-installation)
- [ER Diagram](#markdown-header-er-diagram)
- [API Endpoints](#markdown-header-api-endpoints)
  - [1. Get Potential Duplicates](#markdown-header-1-get-potential-duplicates)
  - [2. Get List of Funds](#markdown-header-2-get-list-of-funds)
  - [3. Get Fund Details](#markdown-header-3-get-fund-details)
  - [4. Update Fund Information](#markdown-header-4-update-fund-information)
- [Tests and Commands](#markdown-header-tests-and-commands)
- [Technical Informations](#markdown-header-technical-informations)
  - [Events](#markdown-header-events)
  - [Scalability](#markdown-header-scalability)

## Overview

There are two ways to test the API endpoints:

**1. Online Method**
```
APP: https://douglas.a8brands.com/
API: https://douglas.a8brands.com/api/funds
```

**2. Local Method**

Given that this application was developed using Laravel, you can clone this repository and install it on your local machine.
To do this, navigate to the [installation section](#markdown-header-instalation).
Running the application locally offers the advantage of executing some [custom commands and unit tests](#markdown-header-tests-and-commands) that I have implemented.


## Installation

Follow these steps to set up and run this Laravel application:

**1. Clone the Repository:**
    ```
    git clone https://douglas_soriano@bitbucket.org/douglas_soriano/canoe-tech-assessment.git .
    ```

**2. Install Composer Dependencies:**
    ```
    composer install
    ```

**3. Create Environment File:**
    ```
    cp .env.example .env
    ```

    Configure the DB variables: DB_DATABASE, DB_USERNAME, DB_PASSWORD.

**4. Generate Application Key:**
    ```
    php artisan key:generate
    ```

**5. Run Migrations:**
    ```
    php artisan migrate
    ```

**6. Start the Development Server:**
    ```
    php artisan serve
    ```

**7. Access the API Endpoints:**
    ```
    http://localhost:8000/api
    ```

**8. (Optional) Populate the Database:**
    ```
    php artisan dummy:populate 1000
    ```


## ER Diagram

You can find the Entity Relationship (ER) Diagram used for this application at the following link:
```
https://miro.com/app/board/uXjVN9_xdHM=/?share_link_id=659916370064
```


## API Endpoints

### 1. Get Potential Duplicates

- **Description:** Retrieve a list of potentially duplicated funds.
- **Endpoint:** `/api/funds/potential-duplicates`
- **Method:** `GET`
- **Parameters:**
  - None

### 2. Get List of Funds

- **Description:** Retrieve a list of funds based on specified parameters.
- **Endpoint:** `/api/funds`
- **Method:** `GET`
- **Parameters:**
  - `name` (optional): Filter funds by name.
  - `start_year` (optional): Filter funds by start year.
  - `fund_manager_id` (optional): Filter funds by fund manager ID.
  - `fund_manager_name` (optional): Filter funds by fund manager name.

### 3. Get Fund Details

- **Description:** Retrieve details of a specific fund.
- **Endpoint:** `/api/funds/{fund_id}`
- **Method:** `GET`
- **Parameters:**
  - `fund_id` (required): ID of the fund to retrieve details.

### 4. Update Fund Information

- **Description:** Update information for a specific fund.
- **Endpoint:** `/api/funds/{fund_id}`
- **Method:** `PUT`
- **Parameters:**
  - `fund_id` (required): ID of the fund to update.
  - `name` (optional): New name for the fund.
  - `start_year` (optional): New start year for the fund.
  - `fund_manager_id` (optional): New fund manager ID for the fund.
  - `aliases` (optional): Array of new aliases for the fund. `['alias1', 'alias2']`



## Tests and Commands

In order to run our Unit Tests for all the endpoints on this application, simple use the following command:
```
php artisan test
```
It will erase all the database data and generate some records for testing purpose.

You can also populate the database with dummy data using the following command:
```
php artisan dummy:populate 1000
```

## TECHNICAL INFORMATIONS

## Events

Everytime a fund is updated we run a duplicate test to see if the same manager has another fund with the same name or alias.
If so, an event is emitted and you can check this code at:

1. Check for duplicates on fund update.
[app/Http/Controllers/API/FundController.php](https://bitbucket.org/douglas_soriano/canoe-tech-assessment/src/master/app/Http/Controllers/API/FundController.php#lines-97)

2. Throw event if it has a potential duplicated fund.
[app/Events/DuplicateFundWarning.php](https://bitbucket.org/douglas_soriano/canoe-tech-assessment/src/master/app/Events/DuplicateFundWarning.php)

3. Listen to the event and do something. *- We're just logging the duplicate for now.*
[app/Listeners/DuplicateFundWarningListener.php](https://bitbucket.org/douglas_soriano/canoe-tech-assessment/src/master/app/Listeners/DuplicateFundWarningListener.php)

Also we can check for potential duplicates by calling the API endpoint:
```
/api/funds/potential-duplicates
```
There's room for improvement, but for a test assessment, it should work just fine, even with large databases.


## Scalability

***- How will your application work as the data set grows increasingly larger?***

**Laravel Framework:** I've been using Laravel for large applications for years now, and I love it.

**Table INDEX:** For this application, I carefully selected all the INDEXES on each table in the database to speed up our queries.

**Laravel Eager Loading:** I've applied it to every database query to ensure that we don't have multiple queries being unnecessarily called.

#
***- How will your application work as the # of concurrent users grows increasingly larger?***

**Rate Limiting:** I use rate limiting on API endpoints to prevent abuse and protect against potential DDoS attacks.

**Input Validation:** I've utilized Laravel's validation features to validate and sanitize input data, preventing SQL injection and other security vulnerabilities.

**Optimized Query Parameters:** I've designed API endpoints to accept optimized query parameters, allowing clients to request only the necessary data.




