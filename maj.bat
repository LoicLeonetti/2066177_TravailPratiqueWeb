del var\cache\*.* /s /q > scrap
php bin\console doctrine:schema:update --dump-sql --complete
php bin\console doctrine:schema:update --force --complete