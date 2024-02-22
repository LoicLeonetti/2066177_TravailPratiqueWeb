php bin\console doctrine:database:drop --force
del var\cache\*.* /s /q >scrap
php bin\console doctrine:database:create
php bin\console doctrine:schema:create