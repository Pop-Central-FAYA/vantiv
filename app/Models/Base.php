<?php

namespace Vanguard\Models;

/**
 * This model by default sets attributes so the id field is a string
 * and is non incrementing
 * It will also automatically generate the id.
 * IF there is need to use the default, just override it in the specific model
 * IF the incrementing key is default int, then in your specific model, overwrite
 * the following:
 * `public $incrementing = true;`
 * `protected $keyType = 'int';`
 */
class Base extends \Illuminate\Database\Eloquent\Model {
    protected $connection = 'api_db';

    public $incrementing = false;
    protected $keyType = 'string';

    protected function setFayaSpecificPrimaryKey() {
        if( !$this->getIncrementing() && !$this->getKey()) {
            $new_key = uniqid();
            $this->setAttribute($this->getKeyName(), $new_key);
        }
    }

    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query) {
        $this->setFayaSpecificPrimaryKey();
        return parent::performInsert($query);
    }
}
