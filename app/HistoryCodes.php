<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryCodes extends Model
{
    protected $table = 'history_codes';
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class,'client_id');
    }

}
