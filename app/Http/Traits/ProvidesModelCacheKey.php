<?php

namespace Vanguard\Http\Traits;

trait ProvidesModelCacheKey
{
    /**
     * Cache Key
     */
    public function cacheKey()
    {
        return sprintf(
            "%s/%s-%s",
            $this->getTable(),
            $this->getKey(),
            $this->updated_at->timestamp
        );
    }
}
