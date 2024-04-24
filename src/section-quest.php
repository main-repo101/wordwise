<?php

use shs\project_wordwise\controller\ParticipantHandler;
use shs\project_wordwise\controller\QuestAndAnswerHandler;
use shs\project_wordwise\database\QuestAndAnswerSingleMap;
use shs\project_wordwise\model\Status;
use shs\project_wordwise\model\questionnaire\Quest;
use shs\project_wordwise\model\questionnaire\QuestType;

require_once( __DIR__ . '/module-info.php' );


header('Content-Type: application/json');

//REM: TODO-HERE; remove boilerplates, rafactor it later....
if( $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $requestBodyType = (String) $data['request_body_type'];
    $questType = (String) $data['quest_type'];

    $qH = new QuestAndAnswerHandler();
    $pH = new ParticipantHandler();

    if( $requestBodyType === RequestBodyType::CHECK_SESSION  ) {
        $qH->generateInitQnA();

        $participant = $pH->searchByUsername( $_SESSION['user_info']['username']?? Status::NA['VALUE'], $questType );
        if($participant !== false ) {
            $participant->updateSession();            
        }
        
        $qAMap = $qH->generateQuest( $questType );
        
        //REM: TODO-HERE; fix the condition....
        if( $qH ) {
            echo encodeSessionToJSON( false, 'Successfully generate new quests INIT.');
        }
        else
            echo encodeSessionToJSON( true, "Something went wrong with generating quests INIT"); //REM: TODO-HERE; add more concrete error log. 
    }
    else if(  $requestBodyType === RequestBodyType::CHECK_ANSWER ) {

        $questId = (String) $data['qa_id'];
        $yourQuestAnswer = ( ( is_array( $data['your_answer'] ) ) ? (array) $data['your_answer']
            :  [(String) $data['your_answer']] );

        $qAMap = new QuestAndAnswerSingleMap();
        $qAMap->absorb( 
            $questId, 
            Status::NA['VALUE'],
            [Status::NA['VALUE']],
            $questType,
            null,
            null,
            $yourQuestAnswer
        );

        $qH->verifyAnswer( $qAMap );

        $participant = $pH->searchByUsername( $_SESSION['user_info']['username']?? Status::NA['VALUE'], $questType);
        //REM: Refactor it later...
        if( $_SESSION['answer_server']['is_correct']?? false && $participant !== false ) {
            $participant->score->setTotalItems($_SESSION['quest_info']['total_items']?? 0 );
            $participant->score->update( 1 );
            $pH->update( $participant, $questType );
        } 
        //REM: Refactor it later...
        if( $participant !== false ) {
            $participant->updateSession();
        }
        //REM: TODO-HERE; fix it, not working as intended....
        // if( $qH )
        if( $_SESSION['answer_server']['is_correct']?? false )
            echo encodeSessionToJSON(false, 'Correct Answer');
        else
            echo encodeSessionToJSON(true, 'Incorrect Answer');
    } else if ( $requestBodyType === RequestBodyType::CHECK_QUEST ) {

        
        $qAMap = $qH->generateQuest( $questType );

        $items_left = $_SESSION['quest_info']['current_item_number']?? 1;
        // if( $items_left <= 0 ) {
        //     echo encodeSessionToJSON( true, 'Already Done, re-log-in to restart.');
        //     exit();
        // }
        
        $userInfoScorePoint = $_SESSION['user_info']['score_info']['points']?? 0;
        $questCurrentItemNumber = $_SESSION['quest_info']['current_item_number']?? 0;
        if( $questCurrentItemNumber === 1 && $userInfoScorePoint >= 1 || $qAMap === false ){ //REM: TODO-HRERE
            echo encodeSessionToJSON( 
                true, "All current <span class=\"text-primary\">'{$questType}_TEST'</span> tasks have been completed.<br>
                <span style=\"color: orange;\">Kindly inform your operator that you have completed your task.</span>"
            );
            // echo encodeSessionToJSON( 
            //     true, "Already Done all the Current '$questType' test, log-out and it will be reseted.<br>
            //     <span style=\"color: red;\">Please be mindful that the proposed solution has not undergone thorough testing to ensure its effectiveness.</span>"
            // );
            exit();
        } 
        else if( $qAMap !== false ) { //REM: TODO-HRERE
            echo encodeSessionToJSON( false, "Successfully quest generate - items left: $items_left, {$qAMap->QUEST->ID}");
            exit();
        } else {//REM: TODO-HRERE
            echo encodeSessionToJSON( true, "Failed to generate new QUEST");
            exit();
        }

    } else if ( $requestBodyType === RequestBodyType::RESET ) {
        $_SESSION['quest_info']['current_item_number'] = 0;
    }
}

exit();

