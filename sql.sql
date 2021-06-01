CREATE TABLE tokens (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    token VARCHAR(64),
    user_id INT(11),
    timestamp VARCHAR(10)
);