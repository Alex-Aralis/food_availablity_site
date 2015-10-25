USE food_account_data;

CREATE OR REPLACE TABLE account_login_failures (
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
user_name VARCHAR(80) NOT NULL,
ts TIMESTAMP,
FOREIGN KEY (user_name) REFERENCES accounts(user_name)
   ON DELETE CASCADE
   ON UPDATE CASCADE 
);
