<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class Footer extends Model{
    protected $table = 'sh_footer';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_footer');

        parent::__construct($attributes);
    }


}