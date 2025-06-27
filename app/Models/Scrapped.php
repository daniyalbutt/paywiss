<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrapped extends Model
{
    use HasFactory;
    protected $table = 'scrape_leads';
    
    public function scrape_status(){
        return $this->hasOne(ScrappedStatus::class, 'serial', 'serial');
    }
      
}