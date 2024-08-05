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

    public function actUndoSpend()
    {
        self::setAjaxMode();
        $this->game->actUndoSpend();
        self::ajaxResponse();
    }

    public function actGainResource()
    {
        self::setAjaxMode();
        $resource = self::getArg('resource', AT_alphanum, true);
        $this->game->actGainResource($resource);
        self::ajaxResponse();
    }
}
