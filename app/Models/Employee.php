<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'birthdate',
        'birthplace',
        'email',
        'address',
        'phone',
        'NIN',
        'NCN',
        'CNAS',
        'card_issue_date',
        'card_issue_date',
    ];
}
