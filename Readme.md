<details>
<summary>Exercise Description</summary>
## Description
We want you to implement a REST API endpoint that given a list of products, applies some discounts to them and can be filtered.

You are free to choose whatever language and tools you are most comfortable with. Please add instructions on how to run it and publish it in Github.

## What we expect
- Code structure/architecture must fit this use case, as simple or as complex needed to complete what is asked for.
- Tests are a must. Code must be testable without requiring networking or the filesystem. Tests should be runnable with 1 command.
- The project must be runnable with 1 simple command from any machine.
- Strongly advised to set it up via docker, both the sample application and the required infrastructure services that support it (mysql, postgress, redis, etc)
- Explanations on decisions taken

### Given a list of products: 
> Available on ./dataset/products.json

You must take into account that this list could grow to have way more than 20.000 products, and it would be expected that it would remain performant.

The prices are integers for example, 100.00€ would be 10000.

You can store the products as you see fit (json file, in memory, rdbms of choice)

## Given that:
- Products in the boots category have a 30% discount.
- The product with sku = 000003 has a 15% discount.
- When multiple discounts collide, the bigger discount must be applied.

## GET /products
- Can be filtered by category as a query string parameter
(optional) Can be filtered by priceLessThan as a query string parameter, this filter applies before discounts are applied and will show products with prices lesser than or equal the value provided.
- Returns a list of Product with the given discounts applied when necessary
- Must return at most 5 elements. (The order does not matter)

## Product model
price.currency is always EUR
When a product does not have a discount, price.final and price.original should be the same number and discount_percentage should be null.
When a product has a discount price.original is the original price, price.final is the amount with the discount applied and discount_percentage represents the applied discount with the % sign.


### Example product with a discount of 30% applied.
````
{  
    "sku": "000001",  
    "name": "BV Lean leather ankle boots",  
    "category": "boots",  
    "price": {  
        "original": 89000,  
        "final": 62300,  
        "discount_percentage": "30%",  
    "currency": "EUR"  
    }  
 }
````
### Example product without a discount
````
{
    "sku": "000001",
    "name": "BV Lean leather ankle boots",
    "category": "boots",
    "price": {
        "original": 89000,
        "final": 89000,
        "discount_percentage": null,
        "currency": "EUR"
    }
}
````
</details>

# Solution

## Description

The solution is a REST API that receives a list of products and applies some discounts to them. 
The products are stored in a database and the discounts are applied according to the rules described in the exercise description.

The application is build in PHP with the framework Symfony 7.1, uses a MySQL 8.0.40 database to store the products and Redis to cache the products.
Also uses Docker to make it easier to run the application with all the dependencies.

The application has a single endpoint that receives a list of products and returns the products with the discounts applied on the serializer service.

For the tests, the application uses PHPUnit and the tests are made to check the correct behavior of the application. 
It covers the main functionalities of the application, using unit, integration, and e2e tests.

## Installation
### Requirements
> :warning:
> Before start, you need docker installed on your computer and assure you can use *docker-compose* command

### First init
In main folder, you can use:

####  Without command Make 

in Mac/linux
````
make.sh init
````
in Windows
````
.\make init
````
###### if it is not detected correctly, you can use:
````
.\make.bat init
````

### With Make

In main folder, you can use:

````
make init
````

### Description of install

This first command, make the build of all containers in docker and fulfill the database with the first dataset.

## Tests
### Description of tests

This command will run the tests of the application and show the results. 
Some tests are made to check the correct behavior of the application.
The first init command is necessary to run the tests, because it will build the application and the database with the dataset witch is used in the tests.

### Explanation 

The correct approach is configure test database, but in order to make it more simple, the tests are running in the same environment of the application.

Not ideal, but for this case, it is enough.

### Run tests
In main folder, you can use:

####  Without command Make

in Mac/linux
````
make.sh test
````

in Windows
````
.\make test
````

###### if it is not detected correctly, you can use:
````
.\make.bat test
````

### With Make

In main folder, you can use:

````
make test
````


## 

