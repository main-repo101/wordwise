<?php


use shs\project_wordwise\controller\ParticipantHandler;
use shs\project_wordwise\model\Status;

require_once( __DIR__ . '/module-info.php' );

//  [
//     username: 'Partric',
//     participant_id: 'participant-0001
//     is_admin: true,
//     is_log_in: true,
//     quest_info: [
//          'quest_retain': [ qa_0001, qa_0002, qa_0003, qa_0004 ],
//          'current_item_number': 9, //REM: new
//          'qa_id' => 'qa_0001, 
//          'quest_type' => 'PRE_TEST', 
//          'quest_level' => 'EASY_MODE', 
//          'question' => 'What is the capital of France?', 
//          'choices' => ['Paris', 'Berlin', 'London', 'Madrid']
//     ],
//     answer_client: ['qa_id' => 1, 'participant_id', 'username', 'client_answer' => ['Paris']],
//     answer_server: ['qa_id' => 1, 'server_answer' => ['Paris'], 'isCorrect' => true],
//     score_info: [
//          'participant_id' => 'participant-0001', 
//          'rate' => 90, 
//          'points' => 90, 
//          'total_items' => 100, 
//          'each_point_items' => 1,
//          'best_score' => 90 //REM: new
//      ],
//  ]


if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_SESSION['user_info']['username']?? Status::NA['VALUE'];
    
    $pHandler = new ParticipantHandler();

    if( $username !== Status::NA['VALUE'] && $p = $pHandler->searchByUsername( $username ) ) {
        $_SESSION['user_info']['is_logged_in'] = true; //REM: TODO-HERE; why it did not cached
        echo encodeSessionToJSON(false, 'SESSION CONTINUED' );
        exit();
    }
    
    echo encodeToJSON( 
        null,  null,  null, null, 
        null,  
        null, 
        null, 
        null, 
        false, "IT NEED NEW SESSION"
    );
    exit();
} else {
    echo encodeToJSON( 
        null,  null,  false,  
        null,  null,  null, 
        null, null, true,
        "NEEDED REQUEST METHOD TO BE 'POST'"
    );
}

//REM: Example usage:
// $json = encodePrettyJSON(
//     'JohnDoe',
//     true,
//     true,
//     [
//         ['qa_id' => 1, 'quest_type' => 'Multiple Choice', 'quest_level' => 'Easy', 'question' => 'What is the capital of France?', 'choices' => ['Paris', 'Berlin', 'London', 'Madrid']]
//     ],
//     [['qa_id' => 1, 'your_answer' => ['Paris']]],
//     [['qa_id' => 1, 'the_answer' => ['Paris'], 'isCorrect' => true]],
//     ['participant_id' => 123, 'rate' => 4.5, 'points' => 90, 'total_items' => 100, 'each_points_items' => [1, 2, 3, 4, 5]],
//     ['participant_id' => 123, 'rate' => 4.5, 'points' => 90, 'total_items' => 100, 'each_points_items' => [1, 2, 3, 4, 5]]
// );

exit();