<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function hasPermission($permissionSlug)
    {
        // السوبر أدمن لديه جميع الصلاحيات تلقائياً
        if ($this->name === 'Super Admin') {
            return true;
        }
        
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}

