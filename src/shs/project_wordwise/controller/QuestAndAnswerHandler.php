<?php
namespace shs\project_wordwise\controller;

use shs\project_wordwise\database\DBManagement;
use shs\project_wordwise\database\DBQuestionnaireManagement;
use shs\project_wordwise\database\QuestAndAnswerSingleMap;
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Value;
use shs\project_wordwise\model\questionnaire\Level;
use shs\project_wordwise\model\questionnaire\QuestType;
use shs\project_wordwise\model\Status;


//REM: TODO-HERE; refactor them
class QuestAndAnswerHandler extends Objectx {

    public function __construct( ?DBManagement $dbQuestionnaireManagement = null ) {
        parent::__construct();
        $this->dbQuestionnaireManagement = $dbQuestionnaireManagement?? new DBQuestionnaireManagement();
        $this->retainQuest();
        $this->preprocessQuests();

    }
    public function retainQuest( bool $isReset = false ): void {
        $this->questRetain = ( ($isReset)? $this->dbQuestionnaireManagement->getIds()?? [] 
            : ( ($_SESSION['quest_info']['quest_retain'])?? $this->dbQuestionnaireManagement->getIds()?? [] )
        );
// print count( $this->questRetain ) . ' >>>>>>>>>>>>>>>>>>>>' . PHP_EOL;
    }

    /**
     * @return int ```Status::ERROR['CODE'], Status::WARNING['CODE'], & Status::SUCCESS['CODE']```
     * 
     * ```see: \shs\project_wordwise\model\Status ```
     */
    private function generateQnA( String $filename, bool $isReset = false ): int {

        $jsonStrData = file_get_contents($filename);
        
        $data = json_decode($jsonStrData, true);
        if ($data === null)
            return Status::ERROR['CODE']; //REM: echo "Error decoding JSON";
        else {
            $isAbsorbed = false;
        
            foreach ($data as $key => $item) {
        
                if (isset($item['meta_data'])) {
                    $isAbsorbed = $item['meta_data']['is_absorbed']?? false;
        
                    if ($isAbsorbed && !($isReset) )
                        break;
                    
                    $data[$key]['meta_data']['is_absorbed'] = true;
                
                    //REM: echo "Meta Data:\n";
                    //REM: echo "Is Absorbed: " . $data[$key]['meta_data']['is_absorbed'] . "\n";
                    //REM: echo "Default Instruction: " . $data[$key]['meta_data']['default_instruction'] . "\n";
                    //REM: echo "\n";
                } 
                elseif ( isset($item['num']) ) {
                    //REM: echo "Question Number: " . $item['num'] . "\n";
                    //REM: echo "Question: " . $item['question'] . "\n";
                    //REM: echo "Choices: " . implode(", ", $item['choices']) . "\n";
                    //REM: echo "Answers: " . implode(", ", $item['answers']) . "\n";
                    //REM: echo "\n";
                    $qa = new QuestAndAnswerSingleMap();
                    $qa->set(
                        $item['quest_type'],
                        $item['question']??Value::NA['VALUE'],
                        $item['hint'],
                        $item['choices']??[],
                        Level::getObjLevel( $item['quest_level']??Level::asNormal()->VALUE),
                        $item['answers']??[]
                    );
                    $this->dbQuestionnaireManagement->create( $qa );
                }
            }
        
            if( !($isAbsorbed) || $isReset ) {
                $updated_json = json_encode($data, JSON_PRETTY_PRINT);
                file_put_contents($filename, $updated_json);
                //REM: echo sprintf("%s; Value updated successfully\n", self::class);
                if( $isAbsorbed && $isReset ) return Status::WARNING['CODE'];
                return Status::SUCCESS['CODE'];
            } else {
                //REM: echo sprintf( "%s; Warning: Already once absorbed Question and Answers\n", self::class);
                return Status::WARNING['CODE'];
            }
        }
    }

    public function searchByQuestId( String $questId ) : QuestAndAnswerSingleMap | false {
        $qNA = new QuestAndAnswerSingleMap();
        $qNA->absorb(
            $questId,
            Status::NA['VALUE'],
            [Status::NA['VALUE']],
            Status::NA['VALUE'],
            level::asNormal(),
            Status::NA['VALUE'],
            [STATUS::NA['VALUE']]
        );
        return $this->dbQuestionnaireManagement->retreive( $qNA );
    }

    public function search( QuestAndAnswerSingleMap $qNA ) : QuestAndAnswerSingleMap | false {
        return $this->dbQuestionnaireManagement->retreive( $qNA );
    }


    //REM: TODO-HERE; refactor it....
    // public function verifyAnswer( QuestAndAnswerSingleMap $qNA ): QuestAndAnswerSingleMap | false {
    //     if( $theQuestAndAnswer = $this->searchByQuestId( $qNA->QUEST->ID ) ) {
    //         if( array_intersect($theQuestAndAnswer->ANSWER->ANSWERS, $qNA->ANSWER->ANSWERS) ) {
    //             $this->removeRetainQuest( $theQuestAndAnswer->QUEST->ID );
    //             $_SESSION['answer_client'] = [
    //                 'qa_id' => $qNA->QUEST->ID,
    //                 'answer' => $qNA->ANSWER->ANSWERS
    //             ];
    //             $_SESSION['answer_server'] = [
    //                 'qa_id' => $theQuestAndAnswer->QUEST->ID,
    //                 'answer' => $theQuestAndAnswer->ANSWER->ANSWERS,
    //                 'is_correct'  => true
    //             ];
                
    //             $_SESSION['quest_info']['current_item_number'] = $this->currentItemNumber+1;
                
    //             return $theQuestAndAnswer;
    //         }
    //         $_SESSION['answer_client'] = [
    //             'qa_id' => $qNA->QUEST->ID,
    //             'answer' => $qNA->ANSWER->ANSWERS
    //         ];
    //         $_SESSION['answer_server'] = [
    //             'qa_id' => $theQuestAndAnswer->QUEST->ID,
    //             'answer' => $theQuestAndAnswer->ANSWER->ANSWERS,
    //             'is_correct'  => false
    //         ];
            
    //         $_SESSION['quest_info']['current_item_number'] = $this->currentItemNumber+1;
    //         return false;
    //     }
        
    //     $_SESSION['answer_client'] = [
    //         'qa_id' => $qNA->QUEST->ID,
    //         'answer' => $qNA->ANSWER->ANSWERS
    //     ];
    //     $_SESSION['answer_server'] = [
    //         'qa_id' => Status::NA['VALUE'],
    //         'answer' => [Status::NA['VALUE']],
    //         'is_correct'  => false
    //     ];
        
    //     $_SESSION['quest_info']['current_item_number'] = $this->currentItemNumber+1;
    //     return false;
    // }

    /**
     * 
     * @return QuestAndAnswerSingleMap|false if the said quest is exist return it, other-wise false.
     * @note to verify if the said client quest answer is equal to the server answer use; 
     * $_SESSION['quest_info']['answer_server']['is_correct'];
     */
    public function verifyAnswer(QuestAndAnswerSingleMap $qNA): QuestAndAnswerSingleMap | false {
        $sessionData = [
            'qa_id' => $qNA->QUEST->ID,
            'answer' => $qNA->ANSWER->ANSWERS
        ];

        $_SESSION['answer_client'] = $sessionData;

    
        if ( $theQuestAndAnswer = $this->searchByQuestId($qNA->QUEST->ID) ) {
            //REM: arrayOne = [ 1, 2, 3 ];
            //REM: arrayTwo = [2, 4, 5 ];
            //REM: array_intersect( arrayOne, arrayTwo ) === 2 or return []
            $isCorrect = array_intersect($theQuestAndAnswer->ANSWER->ANSWERS, $qNA->ANSWER->ANSWERS);
            $sessionData['qa_id'] = $theQuestAndAnswer->QUEST->ID;
            $sessionData['answer'] = $theQuestAndAnswer->ANSWER->ANSWERS;
            $sessionData['is_correct'] = (bool)$isCorrect;

            // $this->removeRetainQuest($theQuestAndAnswer->QUEST->QUEST_TYPE, $theQuestAndAnswer->QUEST->ID);
        } else {
            $sessionData['qa_id'] = Status::NA['VALUE'];
            $sessionData['answer'] = [];
            $sessionData['is_correct'] = false;
        }

// print( 'wow   ' . $qNA->QUEST->ID . PHP_EOL );
        $this->removeRetainQuest($qNA->QUEST->QUEST_TYPE, $qNA->QUEST->ID);
        $this->calcItemNumber( $qNA->QUEST->QUEST_TYPE );

        $_SESSION['answer_server'] = $sessionData;
    
        // $_SESSION['quest_info']['current_item_number'] = $this->currentItemNumber + 1;
    
        return isset($theQuestAndAnswer) ? $theQuestAndAnswer : false;
    }
    

    private function removeRetainQuest( String $questType, String $questId ): bool {
        foreach ($this->organizedQuests[$questType] as $key => $value) {
            if ($key === $questId && ( $i = array_search($questId, $this->questRetain) ) !== false ) {
                unset($this->organizedQuests[$questType][$questId]);
                unset($this->questRetain[$i]);
                $_SESSION['quest_info']['quest_retain'] = $this->questRetain?? []; 
                $_SESSION['quest_info']['total_items_left'] = count($this->organizedQuests[$questType]);
                return true;
            }
        }
        
        // print 's,,, ' . count( $this->questRetain ) . PHP_EOL;
        // print 's,,, ' . count( $this->organizedQuests[$questType] ) . PHP_EOL;
        return false;
    }

    //REM: public function encodeSession(): bool {
    //REM:     if( !isset(...))
    //REM: }

    private function preprocessQuests(): void {
        foreach ($this->questRetain as $qa_id) {
            $quest = $this->searchByQuestId($qa_id);
            if ($quest) {
                $type = $quest->QUEST->QUEST_TYPE;
                if (!isset($this->organizedQuests[$type])) {
                    $this->organizedQuests[$type] = [];
                }
                $this->organizedQuests[$type][$qa_id] = $quest;
            }
        }

        //REM: TODO-HERE; refactor it...
        if( $this->organizedQuests[QuestType::PRE]?? false)
            ksort($this->organizedQuests[QuestType::PRE]);
        if( $this->organizedQuests[QuestType::POST]?? false)
            ksort($this->organizedQuests[QuestType::POST]);

    }

    public function getQuestsByType(string $questType): array {
        return $this->organizedQuests[$questType] ?? [];
    }

    //REM: TODO-HERE; rafactor it...
    private function calcItemNumber( String $questType ): void {
        match( $questType ) {
            QuestType::PRE => $questType = 'pre_test',
            default => $questType = 'post_test'
        };
        $_SESSION['quest_info']['current_item_number'] = (++$_SESSION['quest_info'][$questType]['current_item_number']);
    }

    //REM: Generate a single quest of a specific type
    public function generateQuest(string $questType): QuestAndAnswerSingleMap | false {
        
        //REM: TODO-HERE; Refactor it.
        $quests = $this->getQuestsByType($questType);
            
        // print_r( $quests );
        if (empty($quests)) {
            $_SESSION['quest_info']['total_items_left'] = 0; //REM: TODO-HERE; fix it later...
            return false; //REM: No quests of the specified type found
        }

        
        // print '<>' . ( $_SESSION['quest_info']['pre_test']['current_item_number']?? 1 ) . '<>'. PHP_EOL;
        // print '<>' . ( $_SESSION['quest_info']['post_test']['current_item_number']?? 1 ) . '<>'. PHP_EOL;
        $len = count( $quests );

        // print( ">>> " . $len . PHP_EOL );

        //REM: Randomly select a quest from the available quests
        // $randomIndex = array_rand($quests);
        // $selectedQuest = $quests[$randomIndex];

        //REM: get the first element of the said list/array
        $selectedQuest = reset( $quests ); 

        match( $questType ) {
            QuestType::PRE => $questType = 'pre_test',
            default => $questType = 'post_test'
        };

        //REM: TODO-HERE; yikes.... fix later, we're in ASAP mode
        $totalItems = $_SESSION['quest_info']['total_items']?? $len;

        //REM: Store quest information in session
        $_SESSION['quest_info'] = [
            'qa_id' => $selectedQuest->QUEST->ID,
            'pre_test' => [ 'current_item_number' => ( ( $_SESSION['quest_info']['pre_test']['current_item_number'] )?? 1 )],
            'post_test' => ['current_item_number' => ( ( $_SESSION['quest_info']['post_test']['current_item_number'] )?? 1 )],
            'current_item_number' => ( ( $_SESSION['quest_info'][$questType]['current_item_number'] )?? 1 ),
            'total_items' => $totalItems,
            'total_items_left' => $len,
            'quest_retain' => $this->questRetain ?? [],
            'quest_type' => $selectedQuest->QUEST->QUEST_TYPE,
            'quest_level' => $selectedQuest->QUEST->getLevel(),
            'question' => $selectedQuest->QUEST->QUESTION,
            'choices' => $selectedQuest->QUEST->CHOICES
        ];

        
        return $selectedQuest;
    }


    public function generateInitQnA( bool $isReset = false ): int {
        
        //REM: $filenamePreTest = __PROJECT_PRIVATE_RESOURCE_DIR . DIRECTORY_SEPARATOR . 
        //REM:     'init' . DIRECTORY_SEPARATOR .'pre-test-init.json';
        //REM: $filenamePostTest = __PROJECT_PRIVATE_RESOURCE_DIR . DIRECTORY_SEPARATOR . 
        //REM:     'init' . DIRECTORY_SEPARATOR .'post-test-init.json';

        $statusPreTest = $this->generateQnA( self::TEST_INIT_FILENAME['PRE_TEST'], $isReset );
        $statusPostTest = $this->generateQnA( self::TEST_INIT_FILENAME['POST_TEST'], $isReset );

        //REM: TODO-HERE; refactor it...
        if( $isReset )
            $this->retainQuest( $isReset );
        
        return $statusPreTest & $statusPostTest;
    }

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class,
            false
        );
    }

    private const TEST_INIT_FILENAME = [
        'PRE_TEST' => __PROJECT_PRIVATE_RESOURCE_DIR . DIRECTORY_SEPARATOR . 
            'init' . DIRECTORY_SEPARATOR .'pre-test-init.json',

        'POST_TEST' => __PROJECT_PRIVATE_RESOURCE_DIR . DIRECTORY_SEPARATOR . 
            'init' . DIRECTORY_SEPARATOR .'post-test-init.json'
    ];


    private DBManagement $dbQuestionnaireManagement;
    private ?array $questRetain;
    private ?array $organizedQuests;


}