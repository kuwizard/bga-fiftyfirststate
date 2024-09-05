<?php

namespace STATE\Models;

use STATE\Managers\Connections;

class Connection implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var Act
     */
    protected $action;
    /**
     * @var int
     */
    protected $copies;

    public function __construct($params = [])
    {
        if (isset($params['connection_id'])) {
            $params['id'] = $params['connection_id'];
        }
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCopies()
    {
        return $this->copies;
    }

    /**
     * @return int[]
     */
    public function getSpendRequirements()
    {
        return $this->action->getSpendRequirements();
    }

    public function activate()
    {
        $this->action->activate(null);
        Connections::discard($this->id);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'sprite' => Connections::getSprite($this->type),
        ];
    }
}
