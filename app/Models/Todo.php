<?php
namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $table = 'todo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'due_date', 'attachment'
    ];

    protected static function booted()
    {
        if(!app()->runningInConsole()) {
            static::creating(function ($todo) {
                $todo->user_id = Auth::user()->id;
            });
        }
    }    
  

    public function scopeIsComplete($query, $complete)
    {
        $query->where('is_complete', $complete);
    } 

    public function scopeUser($query)
    {
        $query->where('user_id', Auth::user()->id);
    } 
}