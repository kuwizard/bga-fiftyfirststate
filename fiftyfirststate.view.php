<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * fiftyfirststate.view.php
 *
 */

require_once APP_BASE_PATH . 'view/common/game.view.php';

class view_fiftyfirststate_fiftyfirststate extends game_view
{
    function getGameName()
    {
        return 'fiftyfirststate';
    }

    function build_page($viewArgs)
    {
        // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count($players);
    }
}