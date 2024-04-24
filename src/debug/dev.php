<?php
require_once( '../module-info.php' );


use shs\project_wordwise\controller\QuestAndAnswerHandler;
use shs\project_wordwise\controller\ParticipantHandler;
use shs\project_wordwise\database\DBQuestionnaireManagement;
use shs\project_wordwise\database\QuestAndAnswerSingleMap;
use shs\project_wordwise\model\Participant;
use shs\project_wordwise\model\questionnaire\Level;
use shs\project_wordwise\model\questionnaire\Answer;
use shs\project_wordwise\model\questionnaire\Quest;
use shs\project_wordwise\model\questionnaire\QuestType;
use shs\project_wordwise\model\Status;


echo '::: PHP_DEBUG: ' . __FILE__. PHP_EOL;

$qH = new QuestAndAnswerHandler();
$codeResult = $qH->generateInitQnA();


$theQuest = $qH->generateQuest(QuestType::PRE);

if( $theQuest ) {
    $theAnswer = $theQuest->ANSWER->ANSWERS;
    $yourAnswer = ['T'];

    $currentQuest = new QuestAndAnswerSingleMap();
    $currentQuest->absorb( 
        $theQuest->QUEST->ID, 
        Status::NA['VALUE'],
        [Status::NA['VALUE']],
        $theQuest->QUEST->QUEST_TYPE,
        null,
        null,
        $yourAnswer
    );

    print '<|> QA_ID: ' . ( $theQuest->QUEST->ID ) . PHP_EOL;
    print '<|> THE ANSWER: ' . ( implode(', ', $theAnswer) ) . PHP_EOL;
    print '<|> YOUR ANSWER: ' . ( implode(', ', $yourAnswer) ). PHP_EOL;

    $v = $qH->verifyAnswer( $currentQuest );


    if( $v && ( $_SESSION['answer_server']['is_correct'] ) ) {
        print '<|> CORRECT ANSWER: ' . $v->QUEST->ID . PHP_EOL;
    }
    else 
        print '<|> INCORRECT ANSWER: ' . PHP_EOL;
    
} else {
    print 'EMPTY QUEST' . PHP_EOL;
}
print '<|> total_items: '. $_SESSION['quest_info']['total_items'] . PHP_EOL;
print '<|> current_item_number: '. $_SESSION['quest_info']['current_item_number'] . PHP_EOL;
print '<|> total_items_left: '. $_SESSION['quest_info']['total_items_left'] . PHP_EOL;
print '<|> current_item_number pre-test: '. $_SESSION['quest_info']['pre_test']['current_item_number'] . PHP_EOL;
print '<|> current_item_number post-test: '. $_SESSION['quest_info']['post_test']['current_item_number'] . PHP_EOL;


require_once( '../section-selection-sign-out.php' );