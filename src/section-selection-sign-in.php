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

    if( ( $p2 = $pHandler->searchByUsername( $p->getUsername() ) ) ) {
        $p2->setActive(true);
        $p2->updateSession();
        echo encodeSessionToJSON( false, "Successfully Log-in");
        exit();
    }
    else if( $pHandler->addByUsername( $p->getUsername() ) ) {
        $p->setActive(true);
        $p->updateSession();
        echo encodeSessionToJSON( false, "Successfully Registered" );
        exit();
    } else
        echo encodeSessionToJSON( true, "Failed to create an account" );

} else {
    echo encodeToJSON( 
        null,  null,  false,  
        null,  null,  null, 
        null, null, true,
        "NEEDED REQUEST METHOD TO BE 'POST'"
    );
}

exit();