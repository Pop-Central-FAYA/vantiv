<?php

namespace Vanguard\Pagination;
use Illuminate\Pagination\BootstrapThreePresenter;

class HDPresenter extends BootstrapThreePresenter {

    public function render()
    {
        if ($this->hasPages()) {
        return sprintf(
            '<div class="pagi-custom"><div class="pull-left">%s %s</div> <div class="pull-right">%s %s</div></div>',
            $this->getFirst(),
            $this->getButtonPre(),
            $this->getButtonNext(),
            $this->getLast()
            );
        }
        return "";
    }

    public function getLast()
    {
        $url = $this->paginator->url($this->paginator->lastPage());
        $btnStatus = '';

        if($this->paginator->lastPage() == $this->paginator->currentPage()){
            $btnStatus = 'disabled';
        }
        return $btn = "<a href='".$url."'><button class='btn btn-success ".$btnStatus."'>Last <i class='glyphicon glyphicon-chevron-right'></i></button></a>";
    }

    public function getFirst()
    {
        $url = $this->paginator->url(1);
        $btnStatus = '';

        if(1 == $this->paginator->currentPage()){
            $btnStatus = 'disabled';
        }
        return $btn = "<a href='".$url."'><button class='btn btn-success ".$btnStatus."'><i class='glyphicon glyphicon-chevron-left'></i> First</button></a>";
    }

    public function getButtonPre()
    {
        $url = $this->paginator->previousPageUrl();
        $btnStatus = '';

        if(empty($url)){
            $btnStatus = 'disabled';
        }
        return $btn = "<a href='".$url."'><button class='btn btn-success ".$btnStatus."'><i class='glyphicon glyphicon-chevron-left pagi-margin'></i><i class='glyphicon glyphicon-chevron-left'></i> Previous </button></a>";
    }

    public function getButtonNext()
    {
        $url = $this->paginator->nextPageUrl();
        $btnStatus = '';

        if(empty($url)){
            $btnStatus = 'disabled';
        }
        return $btn = "<a href='".$url."'><button class='btn btn-success ".$btnStatus."'>Next <i class='glyphicon glyphicon-chevron-right pagi-margin'></i><i class='glyphicon glyphicon-chevron-right'></i></button></a>";
    }

}
