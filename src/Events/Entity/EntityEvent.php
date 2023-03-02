<?php

namespace EcommerceLayer\Events\Entity;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class EntityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Model $entity;

    /**
     * Create a new event instance.
     */
    public function __construct(Model $entity)
    {
        $this->entity = $entity;
    }

    public function setEntity(Model $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
