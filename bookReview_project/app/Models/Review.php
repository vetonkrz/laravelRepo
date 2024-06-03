<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['review', 'rating'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public static function booted() :void
    {
        static::updated(fn(Review $review) => cache()->forget('book:'.$review->book_id));
        static::deleted(fn(Review $review) => cache()->forget('book:'.$review->book_id));
        static::created(fn(Review $review) => cache()->forget('book:'.$review->book_id));
    }
}
