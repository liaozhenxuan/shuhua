<?php
namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

Class Category extends Model{
    use AdminBuilder,ModelTree{
        ModelTree::boot as treeBoot;
    }
    protected $table = 'category';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('sh_category');

        parent::__construct($attributes);
    }


}