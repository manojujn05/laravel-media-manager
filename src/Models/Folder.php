<?php

namespace Innopanda\AssetManager\Models;


use Illuminate\Database\Eloquent\Model;


class Folder extends Model
{

protected $table = 'asset_folders';


protected $fillable = [
    'name',
    'slug',
    'parent_id',
    'description',
    'sort_order',
];



public function parent()
{
    return $this->belongsTo(
        Folder::class,
        'parent_id'
    );
}



public function children()
{
    return $this->hasMany(
        Folder::class,
        'parent_id'
    );
}


public function childrenRecursive()
{
    return $this->children()
        ->with('childrenRecursive');
}



public function assets()
{
    return $this->hasMany(
        Asset::class
    );
}


}