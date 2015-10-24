USE mysql

DELETE FROM user WHERE User!='root' AND User!='food_user';
DELETE FROM roles_mapping WHERE User!='root';
FLUSH PRIVILEGES;

USE food_account_data;

DELETE FROM accounts;
