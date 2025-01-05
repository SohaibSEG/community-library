<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{

    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    protected $fillable = [
        'book_id',
        'borrower_id',
        'lender_id',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function lender()
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    public function lending()
    {
        return $this->belongsTo(Lending::class);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
