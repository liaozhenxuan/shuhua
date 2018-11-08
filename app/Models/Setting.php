<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Setting extends Model{
    protected $table = 'sh_setting';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_setting');

        parent::__construct($attributes);
    }

}