<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenuAccess extends Model
{
    protected $fillable = ['role_name', 'route_name'];

    protected $table = 'role_menu_access';
}
