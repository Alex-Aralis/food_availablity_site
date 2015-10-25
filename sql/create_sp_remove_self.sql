USE food_account_data

DELIMITER //

CREATE OR REPLACE PROCEDURE remove_self()
MODIFIES SQL DATA
BEGIN
DECLARE sess INT;
DECLARE cont INT DEFAULT 1;
DECLARE sess_host VARCHAR(60);
DECLARE sess_user_name VARCHAR(80);
DECLARE rogue_connections CURSOR FOR 
    SELECT id FROM INFORMATION_SCHEMA.processlist JOIN (
        SELECT conn_id FROM connections WHERE conn_id<>CONNECTION_ID() AND user_name=sess_user_name AND host=sess_host
    ) as a ON id=a.conn_id;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET cont = 0;

CALL split_user(sess_user_name, sess_host);

/*what follows is equivalent to drop user, but drop user cannot be used here because it would require
  a user variable to be create.  and it will not work any other way... */
DELETE FROM mysql.user WHERE user!='root' AND user=sess_user_name AND host=sess_host;
DELETE FROM mysql.roles_mapping WHERE User!='root' AND user=sess_user_name AND host=sess_host;

FLUSH PRIVILEGES;
/*user dropping is complete*/


/*open season on the rogue connections that the user could still have connected.
  happy hunting routine...*/
/*The following code is specially designed to kill all connections 
  of the user being removed.  MySQL, and by extension mariadb, will not
  do this for you EVEN if drop user is used ("by design").  There are 
  a number of spceial cases accounted for that may not be obvious at 
  first glance.  modify with care.*/
OPEN rogue_connections;

/*SQLEXCEPTIONS forced to continue so ALL rogue connections are killed, 
  even if one in the connections table was already disconnected.*/
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SHOW ERRORS;

    FETCH rogue_connections INTO sess;

    /*Kill all of this users connections except for the one running this procedure*/
    WHILE cont = 1 DO
        KILL sess;
        FETCH rogue_connections INTO sess;
    END WHILE;
END;

CLOSE rogue_connections;

/*finally remove user from accounts table, which causes
  cascade that will delete them from all other tables
  in the food_account_data schema.  placed here for
  so it could only be called once.*/
DELETE FROM accounts WHERE user_name=sess_user_name;

/*Kill this connection, the last of its kind.*/
KILL CONNECTION_ID();

END//

DELIMITER ;
