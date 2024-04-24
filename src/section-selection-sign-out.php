<?php

require_once( __DIR__ . '/module-info.php' );
// use shs\project_wordwise\controller\ParticipantHandler;
// use shs\project_wordwise\model\Participant;
// use shs\project_wordwise\model\questionnaire\QuestType;
// use shs\project_wordwise\model\Status;

print( ':::PHP_DEBUG: ' . __FILE__ . PHP_EOL );

// $pH0 = new ParticipantHandler();
// $p0 = new Participant( Status::NA['VALUE'] );
// $p0->decodeSession();
// $pH0->update( $p0, QuestType::PRE );
// $pH0->update( $p0, QuestType::POST );


session_unset();

session_destroy(); 


// print ':::PHP_DEBUG: session save path: ' . session_save_path() . PHP_EOL ;
// print ':::PHP_DEBUG: session file name: sess_' . session_id() . PHP_EOL ;
// print ':::PHP_DEBUG: ';
// print_r($_SESSION);