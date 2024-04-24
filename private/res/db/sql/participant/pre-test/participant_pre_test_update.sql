UPDATE participant_pre_test 
    SET password = :password,
        first_name = :first_name, middle_name = :middle_name, last_name = :last_name, age = :age, 
        rate = :rate, points = :points, total_item_points = :total_item_points, 
        total_items = :total_items, each_item_points = :each_item_points
    WHERE user_name = :user_name;
