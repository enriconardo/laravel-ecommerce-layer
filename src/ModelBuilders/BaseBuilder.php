<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

abstract class BaseBuilder
{
    protected Model $model;

    private bool $transaction;

    abstract public static function getModelClass(): string;

    /**
     * @param Model|null $model
     * @return static
     */
    public static function init($model = null, $transaction = true)
    {
        $class = static::getModelClass();

        $instance = new static;
        $instance->transaction = $transaction;
        $instance->model = is_null($model) ? new $class() : $model;

        if ($instance->transaction) {
            DB::beginTransaction();
        }

        return $instance;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Return whether the transaction is enabled for the current builder.
     * 
     * @return bool
     */
    public function getTransaction()
    {
        return $this->transaction;
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

        if ($this->transaction) {
            DB::commit();
        }

        return $this->model;
    }

    public function abort()
    {
        if ($this->transaction) {
            DB::rollBack();
        }
    }
}
