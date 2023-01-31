<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

abstract class BaseBuilder
{
    protected $model;

    public static function getModelClass() {}

    /**
     * @param Model|null $model
     * @return static
     */
    public static function init($model = null)
    {
        $class = static::getModelClass();

        DB::beginTransaction();

        $instance = new static;
        $instance->model = is_null($model) ? new $class() : $model;

        return $instance;
    }

    /**
     * @param array $attributes
     * @return $this
     * @throws Exception
     */
    public function fill(array $attributes)
    {
        try {
            $this->model = $this->model->fill(attributes_filter($attributes));
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return $this;
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function end()
    {
        try {
            $this->model->save();
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        DB::commit();

        return $this->model;
    }

    public function abort()
    {
        DB::rollBack();
    }
}