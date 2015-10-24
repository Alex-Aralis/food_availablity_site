USE food_account_data;

DELIMITER //

CREATE OR REPLACE PROCEDURE add_session (new_user VARCHAR (80), new_enc_key BINARY(32), new_iv BINARY(16), 
                              max_user_sessions INT, expiration_hours INT) 
MODIFIES SQL DATA
BEGIN
INSERT INTO account_sessions SET user_name=new_user, pw_enc_key=new_enc_key, iv=new_iv;
DELETE FROM account_sessions WHERE user_name=new_user AND ts<DATE_SUB(NOW(), INTERVAL expiration_hours HOUR);

delete from account_sessions where (id) in  (
    select d.id from (
        select a.id, b.id as test from (
            select id from account_sessions where user_name=new_user
        ) as a left join (
            select id from account_sessions where user_name=new_user order by ts DESC limit max_user_sessions) 
        as b on a.id=b.id) 
    as d where d.test is null);

END//

DELIMITER ;
