<?php namespace Filipac\Banip\Models;

use Model;

/**
 * Ip Model
 */
class Ip extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'filipac_banip_ips';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}