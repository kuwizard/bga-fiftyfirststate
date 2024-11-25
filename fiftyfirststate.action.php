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

    public function actPassStoringResource()
    {
        self::setAjaxMode();
        $this->game->actPassStoringResource();
        self::ajaxResponse();
    }

    public function actChooseResourceToStore()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actChooseResourceToStore($resource);
        self::ajaxResponse();
    }

    public function actActivateLocation()
    {
        self::setAjaxMode();
        $this->game->actActivateLocation($this->getId());
        self::ajaxResponse();
    }

    public function actDevelop()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actDevelop($resource);
        self::ajaxResponse();
    }

    public function actDevelopChooseFromHand()
    {
        self::setAjaxMode();
        $this->game->actDevelopChooseFromHand($this->getId());
        self::ajaxResponse();
    }

    public function actDevelopChooseDestination()
    {
        self::setAjaxMode();
        $this->game->actDevelopChooseDestination($this->getId());
        self::ajaxResponse();
    }

    public function actOptionOpenProduction()
    {
        self::setAjaxMode();
        $this->game->actOptionOpenProduction();
        self::ajaxResponse();
    }

    public function actOptionRaze()
    {
        self::setAjaxMode();
        $this->game->actOptionRaze();
        self::ajaxResponse();
    }

    public function actChooseResourceToSteal()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actChooseResourceToSteal($resource);
        self::ajaxResponse();
    }

    public function actChoosePlayerAndResourceToSteal()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $pId = self::getArg('pId', AT_posint, true);
        $this->game->actChoosePlayerAndResourceToSteal($resource, $pId);
        self::ajaxResponse();
    }

    public function actChooseDeal()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actChooseDeal($resource);
        self::ajaxResponse();
    }
}
