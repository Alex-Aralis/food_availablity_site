USE food_account_data;

DELIMITER //

CREATE OR REPLACE PROCEDURE add_connection() 
MODIFIES SQL DATA
BEGIN
DECLARE max_conns INT;
DECLARE sess_user_name VARCHAR(80);
DECLARE sess_host VARCHAR(60);

CALL split_user(sess_user_name, sess_host);

UPDATE connections SET active=FALSE WHERE active=TRUE AND (conn_id) IN (
    SELECT conn_id FROM (
            (SELECT id FROM INFORMATION_SCHEMA.processlist) AS c
        RIGHT JOIN
            (SELECT conn_id, ts FROM connections WHERE user_name=sess_user_name AND host=sess_host) AS b
        on c.id=b.conn_id
    ) WHERE (id IS NULL) AND ts<DATE_SUB(NOW(), INTERVAL 10 SECOND)
); 

INSERT INTO connections SET conn_id=CONNECTION_ID(), user_name=sess_user_name, host=sess_host;

DELETE FROM connections WHERE user_name=sess_user_name AND host=sess_host AND active=FALSE;

END//

DELIMITER ;
