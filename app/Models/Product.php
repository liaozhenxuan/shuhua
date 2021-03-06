<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Product extends Model{
    protected $table = 'sh_product';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_product');

        parent::__construct($attributes);
    }

}