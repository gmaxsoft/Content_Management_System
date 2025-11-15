<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class RouteModel extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'url',            //users/edit/{id}
        'method',         //GET lub POST,PUT
        'controller_name',//Nazwa bazowa controlera bez wyrazu Controller.
        'action_name',    //edit,index itp.
        'name',           //users_edit
        'group_type',     //web lub api
        'is_active'       //0 lub 1
    ]; 

    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
    ];
}
