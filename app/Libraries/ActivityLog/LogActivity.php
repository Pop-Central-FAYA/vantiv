<?php

namespace Vanguard\Libraries\ActivityLog;

class LogActivity
{
    protected $logModel;
    protected $description;

    public function __construct($logModel, $description)
    {
        $this->logModel = $logModel;
        $this->description = $description;
    }

    public function log()
   {
       $details =[
           'ip'=> request()->ip(),
           'user_agent' => request()->header('User-Agent')
       ];
       $activity = activity()
           ->causedBy(\Auth::user())
           ->performedOn($this->logModel)
           ->withProperties($details)
           ->log($this->description);
       return true;
   }

}
