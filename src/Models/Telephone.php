<?php

namespace Ogilo\PhoneBook\Models;

use Illuminate\Database\Eloquent\Model;

class Telephone extends Model
{
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function getValueAttribute($value)
    {
    	return clean_isdn($value);
    }
}
