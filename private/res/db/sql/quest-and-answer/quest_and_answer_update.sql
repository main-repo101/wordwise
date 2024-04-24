UPDATE quest_and_answer 
    SET quest_type = :quest_type,
        qa_content_hash = :qa_content_hash,
        quest_level = :quest_level
        -- quest_filename = :quest_filename,
        -- answer_filename = :answer_filename
    WHERE qa_id = :qa_id;