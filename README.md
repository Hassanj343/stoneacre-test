# Instructions on getting started

- Clone repository
- cd to directory and run `composer install`
- Create .env file by duplicating .env.example: `cp .env.example .env`
- Setup app key `php artisan key:generate`
- Update .env with credentials for database & run migrations using `php artisan migrate`
  - Please ensure that queue_driver is set to `database`
  - 


## Commands

- `php artisan stoneacre:import-vehicles  {file}`
  - The file option is required and should be replaced with path to vehicles stock csv
- `php artisan stoneacre:export-ford`
  - Please ensure that following params are filled with valid credentials to ftp server. 
      - `FTP_FORD_EXPORT_HOST`
      - `FTP_FORD_EXPORT_USERNAME`
      - `FTP_FORD_EXPORT_PASSWORD`
