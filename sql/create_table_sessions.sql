use food_account_data;

CREATE OR REPLACE TABLE account_sessions(
id INT AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(80) NOT NULL ,
pw_enc_key BINARY(32) NOT NULL,
iv BINARY(16)  NOT NULL,
ts TIMESTAMP NOT NULL,
FOREIGN KEY (user_name) 
    REFERENCES accounts(user_name)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
