<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'cover_image',
        'description',
        'genre',
        'published_year',
        'condition',
        'status',
        'owner_id',
    ];

    const CONDITION_NEW = 'New';
    const CONDITION_GOOD = 'Good';
    const CONDITION_FAIR = 'Fair';

    const STATUS_AVAILABLE = 'Available';
    const STATUS_LENT = 'Lent';

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }

    public function borrowers()
    {
        return $this->belongsToMany(User::class, 'lendings', 'book_id', 'borrower_id')
                    ->withPivot('borrow_date', 'due_date', 'return_date');
    }

   

    public  function isAvailable()
    {
        return $this->status === 'Available';
    }
}
