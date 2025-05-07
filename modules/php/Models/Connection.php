<?php

namespace Bga\Games\Fiftyfirststate\Models;

use Bga\Games\Fiftyfirststate\Managers\Connections;

class Connection implements \JsonSerializable
{
    protected ?int $id;
    protected ?string $type;
    protected string $name;
    protected Act $action;
    protected int $copies;
    protected array $text;

    public function __construct($params = [])
    {
        if (isset($params['connection_id'])) {
            $params['id'] = $params['connection_id'];
        }
        $this->id = isset($params['id']) ? (int) $params['id'] : null;
        $this->type = $params['type'] ?? null;
        $this->text = [
            TEXT_TYPE => clienttranslate('INSTANT'),
            TEXT_DESCRIPTION => '',
        ];
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

    public function getBonusUi()
    {
        return $this->action->getBonusUi();
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
            'name' => $this->name,
            'text' => $this->text,
        ];
    }
}
