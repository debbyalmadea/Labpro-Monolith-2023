# Labpro Monolith

Monolith Application developer in Express.js to fullfil Labpro phase 3 selection. The repo for single service application can be accessed [here](https://github.com/debbyalmadea/Labpro-Single-Service.git)

> This project is developed in MacOS environment

## Author

Made Debby Almadea Putri
13521153

## How to Run

### Prerequisite
1. php v8.2.8
2. node v19.9.0
3. docker v20
4. psql v14

### Step by step
1. Clone the project repository from GitHub using the following command:
```
git clone https://github.com/debbyalmadea/Labpro-Monolith-2023.git 
cd Labpro-Monolith
```
2. Rename `.env.example` file to `.env`. Feel free to change the value according to your sytem preferences
3. Run the container using `docker-compose up -d`
4. The base path for the API is `<domain>/api`

### Setting up the application and database
1. Open the CLI for the container using the following command:
```
docker ps
docker exec -it <container-id> sh
```
2. Install the vendor with `composer install`
3. Run the migration by `php artisan migrate`
4. Run the seeding by `php artisan db:seed`
5. The password from the seed is always set to `password123` with email `berrygood@gmail.com`
6. Your app should run on port `8000`

### Additional Notes
- If you got postgresql connection error, check your postgresql database and change the `.env` file to match your postgresql settings
- Make sure to set the ports to 8000 as it is the port that is whitelisted by the single service
- If you use single sevice app from docker, change the `SINGLE_SERVICE_API_URL` env to `host.docker.internal`
  
## Design Pattern
1. Template Method 
   This pattern is implemented in `ApiModel` class by providing template for the data management operations (all, find, with, save, etc) while allowing subclasses to override certain methods (e.g., getTable (or table field), getAttributes) to customize behavior specific to each model.
2. Prototype
   This pattern is implemented in `ApiModel` especially in `newInstance` method. It copies the current class with assigned `row` representing the row in the database and `exists` indicating if the row exists in the database. Except those field, everything reminds the same as the copied class. ApiModel will have many subclasses in the future so this pattern lets us copy existing objects without making my code dependent on their classes (subclasses).
3. Facade
   The `Api` class with the methods `get`, `post`, `put`, `patch`, and `delete` provide a simplified interface to interact with the complex API communication and response handling system. It hides the complexity of HTTP communication, response handling, and status code checking. It also acts as a single entry point for clients to interact with the API system
4. Builder
   The `FilterBuilder` is a builder class to filter model that implements `Filterable` interface. It allows clients to construct complex filtering queries step by step and then retrieve the filtered rows based on the criteria built by the client. By using this pattern, we can add more properties or configuration options to the FilterBuilder class in the future without affecting the existing codebase and without making the class constructor overly complex. It also hides the complex constructing of the queried rows and make the code cleaner and readable
5. Chain of Responsibility
   This pattern is implemented in `ExceptionHandlerChain.php` for error handling mechanism where different error are handled appropriately based on their type, and a default error response is provided if no handler in the chain can handle a specific error. By using the Chain of Responsibility pattern, we can easily add or remove new error handlers without modifying the existing code. It also allows us to have a clear separation of concerns for different types of errors and their corresponding responses.

   This pattern also implemented in laravel middleware system where we provide some handlers (functions) to handle a request. Each middleware function (in this case `Authenticate` and `BeforeChecout`) will processes a request, performs specific tasks, and passes control to the next middleware or the final route handler. This chainable pattern allow us to separate responsibility to different handlers making the code cleaner and each function have a more defined responsibility.

## Tech Stack
- vite v4.0.0
- tailwindcss v3.3.2
- bootstrap ui
- laravel v10
- php v8.2.8
- pusher-js v8.3.0 (for realtime update)
- jquery (ajax) v3.6.0 (for polling)

## Endpoint
### Api
1. `GET /api/barang`: get all barang data [ALL]
2. `POST /api/auth/login`: login to the system and get the access token [ALL]
3. `POST /api/auth/regster`: register to the system and get the access token [ALL]
4. `GET /api/auth/self`: get self data [ADMIN]

### Web
1. `GET /`: page katalog barang [USER] or login [ALL]
2. `GET /auth/login`: login page [ALL]
3. `POST /auth/login`: login to the system and get the access token [ALL]
4. `GET /auth/register`: register page [ALL]
5. `POST /auth/register`: register to the system and get the access token [ALL]
6. `GET /logout`: logout from the system [USER]
7. `GET /barang`: page katalog barang [USER]
8. `GET /barang/{id}`: page detail barang [USER]
9. `GET /barang/{id}/checkout`: page checkout barang [USER]
10. `POST /barang/{id}/checkout`: checkout barang [USER]
11. `GET /riwayat-pembelian`: page Riwayat Pembelian [USER]
12. `GET /keranjang`: page Keranjang [USER]
13. `POST /keranjang`: create keranjang [USER]
14. `POST /keranjang/{id}/checkout`: checkout keranjang [USER]
15. `PATCH /keranjang/{id}/jumlah/decrease`: decrease barang count in keranjang [USER]
16. `PATCH /keranjang/{id}/jumlah/increase`: increase barang count in keranjang [USER]
17. `DELETE /keranjang/{id}`: delete keranjang

## BONUS

### B04 - Polling

This app use short polling to update the katalog barang page without refreshing the window

### B05 - Lighthouse

Here's the screenshot for the lighthouse report
1. Login page
![Login page](/assets/lighthouse-login.png)
2. Register page
![Register page](/assets/lighthouse-register.png)
3. Katalog Barang page (96)
![Katalog barang page](/assets/lighthouse-katalog-barang.png)
4. Detail Barang page
![Detail barang page](/assets/lighthouse-detail-barang.png)
5. Riwayat Pembelian page
![Riwayat pembelian page](/assets/lighthouse-riwayat-pembelian.png)
6. Keranjang page
![Keranjang page](/assets/lighthouse-cart-page.png)

### B06 - Responsive Layout

This app is compatible with both desktop and mobile browser. 

### B07 - API Documentation using Swagger

API Documentation can be accessed [here](https://app.swaggerhub.com/apis-docs/ALMADEAPUTRI/labpro-monolith/1.0.0)

> Note this documentation only includes the api routes or the endpoint that return json value. Most of the request from frontend return redirects or view data (html/server side rendering)

### B08 - SOLID Application
1. Single Responsibility
   Similar to the Single Service app, this app also use MVC + Services architecture. By using this architecture, we're adhering to the Single Responsibility principle where the Controller classes are responsible for parsing the request, call the relevant service(s) to perform the required operations, and send the response. Service classes are responsible for handling complex business logic and provide an abstraction layer between the controller and the database. The view classes are responsible for displaying data to the user with the use of html (+ blade). The model classes are responsible for mapping relational database into an object. 
2. Open-Closed Principle
   The implementation of `ErrorHandlerChain` adheres to the Open-Closed Principle because the error handler is designed to be easily extended with new error handlers. Each error handler is represented as a separate class (HttpCustomExceptionHandler, QueryExceptionHandler, ConnectionExceptionHandler) that extends the abstract ErrorHandlerChain class. When adding new error handlers, the existing error handler classes do not need to be modified and new error handler can be added to the chain without modifying the existing error handler classes.
3. Liskov Substitution Principle
   The implementation of subclasses of `ErrorHandlerChain`, `HttpCustomError`, `Barang`, `Riwayat Pembelin`, etc. adheres to the Liskov Substitution Principle. The subclasses for ErrorHandlerChain (HttpCustomExceptionHandler, QueryExceptionHandler, ConnectionExceptionHandler) are replacing their superclass (ErrorHandlerChain) in the chain of responsibility. All subclasses adhere to the same interface defined by the ErrorHandlerChain superclass. 
4. Interface Segregation Principle
   The class `Keranjang` implements the Filterable interface and provides a specific implementation for the scopeFilter and builder method. This allows clients of the Keranjang class to use the filter method to filter the collection of Keranjang instances.

   The class `AuthService` implements `AuthServiceInterface` which extends interfaces for authentication related service. It allows clients to use specific parts of the authentication service without being forced to depend on unnecessary methods or functionality that they don't require.
5. Dependency Inversion Principle
   The Controllers and Services layers only relying on abstractions instead of concrete implementations. The construction and dependency injection are done by laravel itself. This promotes decoupling and flexibility in your application. If there's any changes in the future for the Services or Models layer we don't need to change other layer that depends on it.

### B10 - Automated Testing using Browser Kit Testing

- The test scripts can be seen inside `tests` folder. The unit tests cover 87.5% of Service Layer where the business logic lays (100% of the required specification). The e2e tests cover 6/6 page with exception search bar for the barang page
- To run the test scripts, run the following command
  ```
  php artisan test
  ```
> Note for e2e test make sure there's at least 2 barang with stok > 0 to make sure all test are covered

### B11 - Additional Feature
1. Search feature

   User can search by item's name or company's name
2. Cart feature
   
   User can add items to cart and decrease/increase item's count in the cart page. The total items in the cart are shown on the floating icon and updated in real time using pusher
