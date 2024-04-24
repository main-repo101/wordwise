<?php
namespace shs\project_wordwise\model\questionnaire;
use shs\project_wordwise\model\Objectx;

final class QuestType extends Objectx {

    #[\Override]
    public function hashCode(): String {
        return hash(
            parent::HASH_ALGO_SHA_256,
            self::class . self::PRE . self::POST,
            false
        );
    }

    public const PRE = 'PRE';
    public const POST = 'POST';
}