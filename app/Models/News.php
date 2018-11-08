<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

Class News extends Model{
    protected $table = 'sh_news';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_news');

        parent::__construct($attributes);
    }

}