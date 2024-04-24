-- CREATE TABLE IF NOT EXISTS participant_post_test (
--     -- id INT PRIMARY KEY,
--     participant_id VARCHAR(255) PRIMARY KEY,
--     quest_type VARCHAR(25) DEFAULT 'POST_TEST',
--     user_name VARCHAR(255),
--     password VARCHAR(255),
--     first_name VARCHAR(255),
--     middle_name VARCHAR(255),
--     last_name VARCHAR(255),
--     age INT,
--     rate DECIMAL(10,2),
--     points INT,
--     total_item_points INT,
--     total_items INT,
--     each_item_points INT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );


INSERT INTO participant_post_test(
    participant_id, 
    user_name, 
    password, 
    first_name, 
    middle_name, 
    last_name, 
    age, rate, 
    points, 
    total_item_points, 
    total_items, 
    each_item_points
) VALUES (
    :participant_id, 
    :user_name, 
    :password, 
    :first_name, 
    :middle_name, 
    :last_name, 
    :age, 
    :rate, 
    :points, 
    :total_item_points, 
    :total_items, 
    :each_item_points
);
