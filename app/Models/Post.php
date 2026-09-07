<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

 #[Fillable(['user_id', 'category_id', 'offering_skills', 'looking_skills', 'description', 'status'])]

class Post extends Model
{
   public function user(): BelongsTo
   {
        return $this->belongsTo(User::class);
   }
   public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
