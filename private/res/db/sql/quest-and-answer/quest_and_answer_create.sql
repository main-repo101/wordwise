-- CREATE TABLE IF NOT EXISTS quest_and_answer (
--     qa_id VARCHAR(255) PRIMARY KEY,
--     qa_content_hash VARCHAR(255) UNIQUE,
--     quest_type VARCHAR(255),
--     quest_level VARCHAR(255),
--     quest_filename VARCHAR(255),
--     answer_filename VARCHAR(255)
-- );


INSERT INTO quest_and_answer(qa_id, qa_content_hash, quest_type, quest_level, quest_filename, answer_filename) 
            VALUES (:qa_id, :qa_content_hash, :quest_type, :quest_level, :quest_filename, :answer_filename);
