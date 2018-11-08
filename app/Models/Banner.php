<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Banner extends Model{
    protected $table = 'sh_banner';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_banner');

        parent::__construct($attributes);
    }


}