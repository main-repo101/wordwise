<?php

use shs\project_wordwise\model\Status;

session_start();

define( '__PROJECT_ROOT_DIR', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
define( '__PROJECT_PUBLIC_DIR', __PROJECT_ROOT_DIR . 'public' . DIRECTORY_SEPARATOR );
define( '__PROJECT_PRIVATE_DIR', __PROJECT_ROOT_DIR . 'private' . DIRECTORY_SEPARATOR );
define( '__PROJECT_PUBLIC_RESOURCE_DIR', __PROJECT_PUBLIC_DIR .  'res'. DIRECTORY_SEPARATOR );
define( '__PROJECT_PRIVATE_RESOURCE_DIR', __PROJECT_PRIVATE_DIR. 'res'. DIRECTORY_SEPARATOR );
define( '__PROJECT_PUBLIC_RESOURCE_DB_DIR', __PROJECT_PUBLIC_RESOURCE_DIR .  'db' . DIRECTORY_SEPARATOR );
define( '__PROJECT_PRIVATE_RESOURCE_DB_DIR', __PROJECT_PRIVATE_RESOURCE_DIR .  'db' . DIRECTORY_SEPARATOR );

require_once( __PROJECT_ROOT_DIR . 'vendor/autoload.php' );
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

function encodeToJSON(
    ?string $username = null,
    ?String $participantId = null,
    ?bool $isAdmin = null,
    ?bool $isLoggedIn = null,
    ?array $questInfo = null,
    ?array $answerClient = null,
    ?array $answerServer = null,
    ?array $scoreInfo = null,
    bool $hadError = null,
    String $msg = null
): string {
    $data = [
        'username' => $username ?? Status::NA['VALUE'],
        'participant_id' => $participantId?? Status::NA['VALUE'],
        'is_admin' => $isAdmin ?? false,
        'is_logged_in' => $isLoggedIn ?? false,
        'quest_info' => $questInfo ?? [],
        'answer_client' => $answerClient ?? [],
        'answer_server' => $answerServer ?? [],
        'score_info' => $scoreInfo ?? [],
        'had_error' => $hadError,
        'message' => $msg
    ];

    $json = json_encode($data, JSON_PRETTY_PRINT );

    //REM: Check for JSON encoding errors
    if ($json === false)
        throw new Exception('Error encoding JSON: ' . json_last_error_msg());
    
    return $json;
}

function encodeSessionToJSON( bool $hadError, String $msg = Status::WAITING['VALUE'] ): String {
    return encodeToJSON(
        $_SESSION['user_info']['username']?? Status::NA['VALUE'],
        $_SESSION['user_info']['participant_id']?? Status::NA['VALUE'],
        $_SESSION['user_info']['is_admin']?? false,
        $_SESSION['user_info']['is_logged_in']?? false,
        $_SESSION['quest_info']?? [],
        $_SESSION['answer_client']?? [],
        $_SESSION['answer_server']?? [],
        $_SESSION['user_info']['score_info']?? [],
        $hadError,
        $msg,
    );
}

//REM: temp impl, asap imp....
class RequestBodyType {
    public const CHECK_SESSION = 'check_session';
    public const CHECK_ANSWER = 'check_answer';
    public const CHECK_QUEST = 'check_quest';
    public const RESET = 'reset_quest';
}
