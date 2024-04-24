CREATE TABLE IF NOT EXISTS participant_pre_test (
    -- id INT PRIMARY KEY,
    participant_id VARCHAR(255) PRIMARY KEY,
    quest_type VARCHAR(25) DEFAULT 'PRE_TEST',
    user_name VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    first_name VARCHAR(255),
    middle_name VARCHAR(255),
    last_name VARCHAR(255),
    age INT,
    rate DECIMAL(10,2),
    points INT,
    total_item_points INT,
    total_items INT,
    each_item_points INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


