<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'parent_id'
    ];

    public function child_category()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->with(["child_category"]);

    }
    public function parent_category()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id')->with(["parent_category"]);

    }

    public function product(){
        return $this->hasMany(Product::class);
    }
}
