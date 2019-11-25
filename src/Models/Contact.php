<?php

namespace Ogilo\PhoneBook\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public function emails()
    {
        return $this->hasMany(Email::class);
    }
    public function telephones()
    {
        return $this->hasMany(Telephone::class);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getFirstNameAttribute($value)
    {
    	return ucwords(strtolower($value));
    }
    public function getLastNameAttribute($value)
    {
    	return ucwords(strtolower($value));
    }
    public function getDisplayNameAttribute($value)
    {
    	return ucwords(strtolower($value));
    }
}
