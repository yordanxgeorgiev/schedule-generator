/* @db_name should be initialised before executing (SET @db_name = ... ;)*/
SET @c = CONCAT('CREATE DATABASE ',@db_name); 
PREPARE stmt from @c; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;