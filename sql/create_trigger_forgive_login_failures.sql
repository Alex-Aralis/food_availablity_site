USE food_account_data;

DELIMITER //

CREATE OR REPLACE TRIGGER forgive_login_failures BEFORE INSERT ON account_sessions FOR EACH ROW
BEGIN
    DELETE FROM account_login_failures WHERE user_name=NEW.user_name;
END//

DELIMITER ;
  
