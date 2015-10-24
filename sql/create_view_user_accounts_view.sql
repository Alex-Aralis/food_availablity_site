USE food_account_data;

CREATE OR REPLACE 
ALGORITHM = MERGE
VIEW user_accounts_view AS 
SELECT * FROM accounts 
WHERE concat_ws('@', user_name, 'localhost')=session_user()
WITH CASCADED CHECK OPTION
;
