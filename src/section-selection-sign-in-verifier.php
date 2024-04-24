<?php

use shs\project_wordwise\controller\ParticipantHandler;
use shs\project_wordwise\model\Participant;
use shs\project_wordwise\model\Status;

require_once( __DIR__ . '/module-info.php' );


header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = (string) $data['username'];

    $pHandler = new ParticipantHandler();
    $p = new Participant( Status::NA['VALUE'] );
    $p->setUsername( $username );
    
    
    if( $p->getUsername() === Status::NA['VALUE'] 
        || strlen( $p->getUsername() ) < 5 
        || strlen( $p->getUsername() ) > 16
    ) {
        //REM: FALSE && TRUE
        echo encodeToJSON( 
            $username,  false,  false,  
            null,  null,  null, 
            null, null, true,
            "Username should have at least 5 characters and a limit of 16 characters"
        );
        exit();
    }
    else if( !preg_match( '/^(?!.*(?:_{2,5}|\s{2,}))[\s\w\d!@#$%^&*()+={}[\]|;:",.<>?\/`~]+$/i', $p->getUsername() ) ) {
        //REM: FALSE && TRUE
        echo encodeToJSON( 
            $username,  false,  false,  
            null,  null,  null, 
            null, null, true,
            "Only underscore, but no consecutive."
        );
        exit();
    }
    else if( ( $p2 = $pHandler->searchByUsername( $p->getUsername() ) ) ) {
        //REM: FALSE && FALSE
        echo encodeToJSON( 
            $p2->getUsername(), $p2->getId(),  false,  
            null,  null,  null, 
            null, 
            [
                'participant_id' => $p2->getId(), 
                'rate' => $p2->score->getRate(), 
                'points' => $p2->score->getPoints(), 
                'total_items' => $p2->score->getTotalItems(), 
                'each_points_items' => $p2->score->getEachItemPoints() 
            ], 
            false,
            "Username already existed"
        );
        exit();
    }
    else {
        //REM: TRUE && FALSE
        echo encodeToJSON( 
            $username,  null,  false,  
            null,  null,  null, 
            null, null, false,
            "Username is Available"
        );
        exit();
    }

    // $_SESSION['username'] = $p->getUsername();

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