<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Author extends Model{
    protected $table = 'sh_author';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_author');

        parent::__construct($attributes);
    }


}