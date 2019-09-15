<?php

namespace Vanguard\Services\Ratings;

/**
 * This class jsut returns the count of stations that are available 
 * @todo log how long it takes to calculate these counts
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 */
class TvRatingsCount extends TvRatingsList
{
    
    protected function getQueryType() {
        return "availability_count";
    }

    protected function calculateRatings() {
        $query = $this->filterForRequestedAudience();
        return $query->count();
    }
}