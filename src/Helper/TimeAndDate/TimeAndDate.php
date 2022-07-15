<?php

namespace ArmelWanes\Crudify\Helper\TimeAndDate;
use Carbon\Carbon;
class TimeAndDate
{
    public function addDays($day){
      return Carbon::now()->addDays($day);
    }
    public function addMonth($month){
        if($month==1) return Carbon::now()->addMonth();
        else return Carbon::now()->addMonths($month);

    }
    public function addMonths($month){
        if($month==1) return Carbon::now()->addMonth();
        else return Carbon::now()->addMonths($month);

    }
    public function addYear($year){
        if($year==1) return Carbon::now()->addYear();
        else return Carbon::now()->addYears($year);

    }
    public function addYears($year){
        if($year==1) return Carbon::now()->addYear();
        else return Carbon::now()->addYears($year);

    }
}
