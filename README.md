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
   NASDAQ_LISTED_JSON_URL=https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json
   
   RAPID_API_URL=https://yh-finance.p.rapidapi.com
   RAPID_API_HOST=yh-finance.p.rapidapi.com
   RAPID_API_KEY=
   
   QUEUE_CONNECTION=redis
   
   REDIS_HOST=redis
   REDIS_PASSWORD=
   REDIS_PORT=6379
   
   MAIL_MAILER=smtp
   MAIL_HOST=
   MAIL_PORT=
   MAIL_USERNAME=
   MAIL_PASSWORD=
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="hello@xm.com"
   MAIL_FROM_NAME="${APP_NAME}"
    ```

6. Enter the app container

    ```bash
    docker exec -it app bash
    ```

    1. Install the composer dependencies:

       ```bash
       composer install
       ```

    2. Run the following command to run the tests:

        ```bash
        php artisan test
        ```
