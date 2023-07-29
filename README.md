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
git clone https://github.com/debbyalmadea/Labpro-Monolith.git 
cd Labpro-Monolith
```
2. Rename `.env.example` file to `.env`. Feel free to change the value according to your sytem preferences
3. Run the container using `docker-compose up -d`
4. The base path for the API is `<domain>/api`

### Migration and seeding the database
1. Open the CLI for the container using the following command:
```
docker ps
docker exec -it <container-id> sh
```
2. Run the migration by `php artisan migrate`
3. Run the seeding by `php artisan db:seed`
4. The password from the seed is always set to `password123` with email `berrygood@gmail.com`

### Additional Notes
- If you got postgresql connection error, check your postgresql database and change the `.env` file to match your postgresql settings
- Make sure to set the ports to 8000 as it is the port that is whitelisted by the single service
  
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
- node.js v19.9.0
- vite v4.0.0
- tailwindcss v3.3.2
- daisyui v3.2.1
- laravel v10
- php v8.2.8
- pusher-js v8.3.0 (for realtime update)
- jquery (ajax) v3.6.0 (for polling)

## Endpoint
> Note ALL here means the whitelisted domain only
1. `GET \`: check if server is running [ALL]
2. `POST \login`: login to the system as admin and get the access token [ALL]
3. `GET \self`: get self data (username and name) [ADMIN]
4. `GET \barang`: get all barang data [ALL]
5. `GET \barang\{id}`: get barang data by id [ALL]
6. `POST \barang`: create a new barang [ADMIN]
7. `PUT \barang\{id}`: update barang [ADMIN]
8. `DELETE \barang\{id}`: delete barang [ADMIN]
9. `PATCH \barang\{id}\stok\decrease`: decrease barang's stock (stok) [ALL]
10. `GET \perusahaan`: get all perusahaan data [ALL]
11. `GET \perusahaan\{id}`: get perusahaan data by id [ALL]
12. `POST \perusahaan`: create a new perusahaan [ADMIN]
13. `PUT \perusahaan\{id}`: update perusahaan [ADMIN]
14. `DELETE \perusahaan`: delete perusahaan [ADMIN]

## BONUS

### API Documentation using Swagger

API Documentation can be accessed [here](https://app.swaggerhub.com/apis-docs/ALMADEAPUTRI/labpro-single-service/1.0.0)

### Automated Testing using Jest

- The test scripts can be seen inside `tests` folder. The tests cover 100% of Service Layer where the business logic lays
- To run the test scripts, run the following command
  ```
  npm test
  ```

### SOLID Application
1. Single Responsibility
2. Open-Closed Principle
3. Liskov Substitution Principle
4. Interface Segregation Principle
5. Dependency Inversion Principle