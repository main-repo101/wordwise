<?php

require_once( __DIR__ . '/module-info.php' );

use shs\project_wordwise\controller\QuestAndAnswerHandler;
use shs\project_wordwise\model\Status;


// echo json_encode(['message' => 'wat'], JSON_PRETTY_PRINT);
// exit();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    $questHandler = new QuestAndAnswerHandler();

    $status = $questHandler->generateInitQnA();

    // echo json_encode(['message' => $status ], JSON_PRETTY_PRINT);
    // exit();

    $questHandlerToString = $questHandler->toString();
    echo match( $status ) {
        Status::SUCCESS['CODE'] => encodeToJSON(
            null, null, null, null, null,
            null, null, null, false, sprintf( ":::%s: %s", Status::SUCCESS['VALUE'], $questHandlerToString ) ),
        Status::WARNING['CODE'] => encodeToJSON(
            null, null, null, null, null,
            null, null, null, true, sprintf( ":::%s: %s", Status::WARNING['VALUE'], $questHandlerToString ) ),
        Status::ERROR['CODE'] => encodeToJSON(
            null, null, null, null, null,
            null, null, null, true, sprintf( ":::%s: %s", Status::ERROR['VALUE'], $questHandlerToString ) ),
        default => encodeToJSON( 
            null, null, null, null, null,
            null, null, null, true, 
            sprintf( 
                ":::%s: %s, %s", Status::ERROR['VALUE'], 
                __FILE__ ,
                $questHandlerToString
            )
        )
    };
} else
    echo encodeToJSON(
        null, null, null, null, null,
        null, null, null, true, 
        sprintf( 
            ":::%s: %s, %s", Status::ERROR['VALUE'], 
            __FILE__ ,
            "CLIENT TO SERVER REQUEST METHOD was not POST" 
        )
    );

exit();
    