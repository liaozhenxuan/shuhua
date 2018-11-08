<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Gry extends Model{
    protected $table = 'sh_gry';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_gry');

        parent::__construct($attributes);
    }

}