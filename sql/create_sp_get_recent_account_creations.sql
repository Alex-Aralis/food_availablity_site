USE food_account_data;

DELIMITER //

CREATE OR REPLACE PROCEDURE get_recent_account_creations (request_ip VARCHAR(60), shelf_life INT, OUT recent_accounts INT) 
MODIFIES SQL DATA
BEGIN

DELETE FROM account_creation_ips WHERE timestamp<DATE_SUB(NOW(), INTERVAL shelf_life HOUR) AND ip=request_ip;

SET recent_accounts = 0;
SELECT COUNT(*) INTO recent_accounts FROM account_creation_ips WHERE ip=request_ip GROUP BY ip;

END//

DELIMITER ;
