<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

}
