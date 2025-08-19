<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function salesOn(Carbon|string $date)
    {
        $d = Carbon::parse($date)->toDateString();
        return $this->sales()->whereDate('sold_at', $d);
    }
}
