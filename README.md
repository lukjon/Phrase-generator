# Phrase-generator

Phrase generator creates random sentence according to passed blueprints in PhraseGenerator service.

Launch application locally
1. Download full repository
2. Turn on terminal and navigate to the folder where this repository is extracted.
3. Install your vendor dependencies with command composer install
4. Customize the database settings in .env file
  # add this line
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
5. Create database and run migrations:
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
6. Launch the application symfony serve and you can stop it symfony server:stop
