<?php

namespace App\Dao;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseDao {

    protected Model $model;

    public function __construct() {
        $this->model = resolve($this->getModel());
    }

    abstract protected function getModel(): string;

    public function findAll(): Collection {
        $modelList = $this->model::get();

        foreach ($modelList as $obj) {
            $this->transform($obj);
        }

        return $modelList;
    }

    public function get(string $id): Model {
        $obj = $this->model::find($id);
        $this->transform($obj);
        return $obj;
    }

    public function save(Model $obj): Model {
        if ($obj->id) {
            $obj->_id = $obj->id;
        }
        
        $obj->save();
        $this->transform($obj);
        return $obj;
    }

    public function delete(Model $obj): void {
        $obj->delete();
    }

    protected function transform(Model $obj): void {
        $obj->id = $obj->_id;
        unset($obj->_id);
    }

}
