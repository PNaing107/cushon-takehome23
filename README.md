# Cushon Retail Customer
## Background

Cushon is a FinTech provider of workplace savings and pensions. Through a combination of web and native applications, Cushon offers pensions and investment services to direct members, employers and via intermediaries and payroll providers.

---
## Brief
- Cushon want to expand their Investment ISA offering to retail customers.
- They want to keep the functionality for retail ISA customers separate from their Employer based offering where practical.
- When customers invest into a Cushon ISA they should see a list of available funds
- Currently a retail customer can only select a single fund from this list
- In the future it is expected that a retail customer can pick multiple funds from the list
- Once the customer’s selection has been made, they should also be able to provide details of the amount they would like to invest
- Given the customer has both made their selection and provided the amount the system should record these values and allow these details to be queried at a later date
- As a specific use case please consider a customer who wishes to deposit £25,000 into a Cushon ISA all into the Cushon Equities Fund
---
## Upfront Assumptions for this example solution
- Customer registration and Authentication are handled by another service in the system
- Employer customers and retail customers will not currently interact or share accounts
- A retail customer has successfully opened a Cushon ISA
- Transferring money into a Cushon ISA is handled by another service in the system (the logic for checking ISA allowance is handled by this service)
- There is no lower or upper limit to the amount that a retail customer can deposit into their Cushon ISA
- Withdrawing money from the Cushon ISA is out of scope
- Only serving UK customers so prices are all in GBP
- Customers can only make a single transaction at a time
- When customers buy or sell a fund they can decide how much money to invest or withdraw (fractional shares are allowed) 
---

## Retail Customer Database Schema Proposal
https://miro.com/app/board/uXjVMiUuuGo=/?share_link_id=506487722084

---

## RESTful API Documentation
The API documentation can be found [here](https://app.swaggerhub.com/apis/PHONENAINGDEV/Cushon-Retail-Customer/1.0.0)

---
## Tech Stack

This demo RESTful API has been built using a Slim Framework 4 skeleton. It uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

It also uses a MySQL database and has been tested locally inside a Docker Container.

## Install the Application
Follow the instructions below to test it out locally.

Create a new directory with your project name, e.g:


```bash
mkdir cushonDemo
```

Once inside the new directory, clone this repo:

```bash
git clone git@github.com:PNaing107/cushon-takehome23.git .
```

One cloned, you must install the slim components by running:

```bash
composer install
```

To run the application locally:
```bash
composer start

```
Run this command in the application directory to run the test suite
```bash
composer test
```

Finally setup the database and populate it it with some dummy data by copying the contents of the `retail_customer_db.sql` in the `/docs` folder into your MySQL Client and run it.
