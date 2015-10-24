REVOKE ALL PRIVILEGES, GRANT OPTION FROM food_user;

GRANT SELECT ON food.* TO food_user;

GRANT SELECT ON food_account_data.user_accounts_view to food_user;

GRANT UPDATE(email) ON food_account_data.user_accounts_view to food_user;

GRANT EXECUTE ON PROCEDURE food_account_data.remove_self to food_user;

GRANT EXECUTE ON PROCEDURE food_account_data.add_connection to food_user;

