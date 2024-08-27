<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * fiftyfirststate.action.php
 *
 * 51 State main action entry point
 *
 */
class action_fiftyfirststate extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = 'common_notifwindow';
            $this->viewArgs['table'] = self::getArg('table', AT_posint, true);
        } else {
            $this->view = 'fiftyfirststate_fiftyfirststate';
            self::trace('Complete reinitialization of board game');
        }
    }

    private function getId()
    {
        return (int) self::getArg('id', AT_alphanum, true);
    }

    public function actDiscardCardsGameStart()
    {
        self::setAjaxMode();
        $cardIds = explode(';', self::getArg('ids', AT_numberlist, true));
        $cardIds = array_filter($cardIds);
        $cardIds = array_map(function ($id) {
            return (int) $id;
        }, $cardIds);
        $this->game->actDiscardCardsGameStart($cardIds);
        self::ajaxResponse();
    }

    public function actChooseCardLookout()
    {
        self::setAjaxMode();
        $locationId = self::getArg('id', AT_numberlist, true);
        $this->game->actChooseCardLookout($locationId);
        self::ajaxResponse();
    }

    public function actActionPass()
    {
        self::setAjaxMode();
        $this->game->actActionPass();
        self::ajaxResponse();
    }

    public function actSpendWorkers()
    {
        self::setAjaxMode();
        $this->game->actSpendWorkers();
        self::ajaxResponse();
    }

    public function actUndo()
    {
        self::setAjaxMode();
        $this->game->actUndo();
        self::ajaxResponse();
    }

    public function actGainResourceForWorkers()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actGainResourceForWorkers($resource);
        self::ajaxResponse();
    }

    public function actEnableFactionActions()
    {
        self::setAjaxMode();
        $this->game->actEnableFactionActions();
        self::ajaxResponse();
    }

    public function actFactionAct()
    {
        self::setAjaxMode();
        $this->game->actFactionAct($this->getId());
        self::ajaxResponse();
    }

    public function actUseLocation()
    {
        self::setAjaxMode();
        $this->game->actUseLocation($this->getId());
        self::ajaxResponse();
    }

    public function actLocationBuild()
    {
        self::setAjaxMode();
        $this->game->actLocationBuild();
        self::ajaxResponse();
    }

    public function actLocationRaze()
    {
        self::setAjaxMode();
        $this->game->actLocationRaze();
        self::ajaxResponse();
    }

    public function actLocationDeal()
    {
        self::setAjaxMode();
        $this->game->actLocationDeal();
        self::ajaxResponse();
    }

    public function actDiscardLocation()
    {
        self::setAjaxMode();
        $this->game->actDiscardLocation($this->getId());
        self::ajaxResponse();
    }

    public function actChooseSource()
    {
        self::setAjaxMode();
        $this->game->actChooseSource($this->getId());
        self::ajaxResponse();
    }

}
