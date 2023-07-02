# XM PHP Execrise

## System Requirements

- docker and docker-compose

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/MarouaneZizah/xm-php-exercise
    ```

2. Navigate to the project directory:

   ```bash
   cd xm-php-exercise
   ```

3. Run the following command to start the application:

    ```bash
   docker-compose up -d --build
    ```

4. Generate .env file
   ```bash
   cp .env.example .env
    ```

5. Make sure to set these variables in the .env file

    ```bash
   RAPID_API_KEY=
   
   MAIL_HOST=
   MAIL_PORT=
   MAIL_USERNAME=
   MAIL_PASSWORD=
    ```

6. Enter the app container

    ```bash
    docker exec -it app bash
    ```

    1. Install the composer dependencies:

       ```bash
       composer install
       ```

   2. Generare the application key:

      ```bash
      php artisan key:generate
        ```

   3. Run the migrations:

      ```bash
      php artisan migrate
        ```
   4. Import the Nasdaq listing data:

      ```bash
      php artisan app:import-companies
        ```
      
   5. Run the following command to run the tests:

        ```bash
        php artisan test
        ```
   
   6. Run the job to send the email:

       ```bash
       php artisan queue:work
       ```
