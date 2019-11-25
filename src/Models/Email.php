<?php

namespace Ogilo\PhoneBook\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
