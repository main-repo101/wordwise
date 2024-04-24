<?php
namespace shs\project_wordwise\controller;

use shs\project_wordwise\database\DBParticipantManagement;
use shs\project_wordwise\model\Objectx;
use shs\project_wordwise\model\Participant;
use shs\project_wordwise\model\questionnaire\QuestType;
use shs\project_wordwise\model\Value;

class ParticipantHandler extends Objectx {

    public function __construct( ?DBParticipantManagement $dbParticipantMgmt = null ) {
        parent::__construct();
        $this->init( $dbParticipantMgmt );
    }

    private function init( ?DBParticipantManagement $dbParticipantMgmt = null ) {
        $this->dbParticipantMgmt = $dbParticipantMgmt?? new DBParticipantManagement();
        // $this->dbParticipantMgmt->createTable();
    }

    public function addByUsername( String $username ): bool {
        $participant = new Participant( Value::NA['VALUE'], true );
        $participant->setUsername( $username );
        return $this->add( $participant );
    }

    public function add( Participant $participant ): bool {
        Participant::stopIdCounter( true );
        $this->dbParticipantMgmt->switchTableByQuestType( QuestType::PRE );
        $pPreTest = $this->dbParticipantMgmt->create( $participant );

        $this->dbParticipantMgmt->switchTableByQuestType( QuestType::POST );
        $pPostTest = $this->dbParticipantMgmt->create( $participant );

        Participant::stopIdCounter( !( $pPreTest && $pPostTest ) );

        return $pPreTest && $pPostTest;
    }

    public function searchByUsername( String $username, String $questType = QuestType::PRE ): Participant | bool {
        $p = new Participant( Value::NA['VALUE'], true );
        $p->setUsername( $username );
        return $this->search( $p , $questType );
    }

    public function search( Participant $participant, String $questType = QuestType::PRE ): Participant | false {

        $this->dbParticipantMgmt->switchTableByQuestType( $questType );

        return $this->dbParticipantMgmt->retreive( $participant );
    }

    public function update( Participant $participant, String $questType = QuestType::PRE ): bool {
        // if( $p = $this->search( $participant, $questType ) ) {
        //     $this->dbParticipantMgmt->update( $participant );
        // }
        $this->dbParticipantMgmt->switchTableByQuestType( $questType );
        return $this->dbParticipantMgmt->update( $participant );
    }

    //REM: CAUTION... it will be deprecated, use 'upate( Participant, String ) or create'
    public function updateSessionByUsername( String $username, String $questType = QuestType::PRE ): Participant | false {
        if( $p = $this->searchByUsername( $username, $questType ) )
            return $p->updateSession();
        return false;
    }

    //REM: CAUTION... it will be deprecated, use 'upate( Participant, String ) or create'
    public function updateSession( Participant $participant, String $questType = QuestType::PRE ): Participant | false {
        if( $p = $this->search( $participant, $questType ) )
            return $p->updateSession();
        return false;
    }
    public function delete( Participant $participant, String $questType = QuestType::PRE ): Participant | false {
        $this->dbParticipantMgmt->switchTableByQuestType( $questType );

        return $this->dbParticipantMgmt->delete( $participant );
    }

    #[\Override]
    public function hashCode(): String {
        return hash( 
            parent::HASH_ALGO_SHA_256,
            self::class,
            false
        );
    }

    private DBParticipantManagement $dbParticipantMgmt;
}