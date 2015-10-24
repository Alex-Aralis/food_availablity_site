USE food_account_data

DELIMITER //

CREATE OR REPLACE PROCEDURE remove_self()
MODIFIES SQL DATA
BEGIN
DECLARE sess INT;
DECLARE cont INT DEFAULT 1;
DECLARE sess_host VARCHAR(60);
DECLARE sess_user_name VARCHAR(80);
DECLARE rouge_connections CURSOR FOR 
    SELECT id FROM INFORMATION_SCHEMA.processlist JOIN (
        SELECT conn_id FROM connections WHERE user_name=sess_user_name AND host=sess_host
    ) as a ON id=a.conn_id;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET cont = 0;

CALL split_user(sess_user_name, sess_host);

/*
SET @str = CONCAT("DROP USER '", sess_user_name, "'@'",sess_host , "'");

PREPARE stmt FROM @str;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
*/

DELETE FROM mysql.user WHERE user!='root' AND user=sess_user_name AND host=sess_host;
DELETE FROM mysql.roles_mapping WHERE User!='root' AND user=sess_user_name AND host=sess_host;

FLUSH PRIVILEGES;

OPEN rouge_connections;

DELETE FROM accounts WHERE user_name=sess_user_name;

FETCH rouge_connections INTO sess;

WHILE cont = 1 DO
    KILL sess;
    FETCH rouge_connections INTO sess;
END WHILE;

CLOSE rouge_connections;


END//


DELIMITER ;
