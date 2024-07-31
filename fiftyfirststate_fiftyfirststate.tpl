{OVERALL_GAME_HEADER}

<!--
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stats.inc.php
 *
 * 51 State game statistics description
 *
-->

<script type="text/javascript">
    var jstpl_player_board = `
    <div class="playerResourcesWrapper">
        <div class="cards resource">
            <span class="cardsIcon resourceIcon">
                <span class="cardsValue resourceValue">\${cards}</span>
            </span>
        </div>
        <div class="playerResources">
            <div class="fuel resource">
                <span class="fuelIcon resourceIcon"></span>
                <span class="fuelValue resourceValue">\${fuel}</span>
            </div>
            <div class="gun resource">
                <span class="gunIcon resourceIcon"></span>
                <span class="gunValue resourceValue">\${gun}</span>
            </div>
            <div class="iron resource">
                <span class="ironIcon resourceIcon"></span>
                <span class="ironValue resourceValue">\${iron}</span>
            </div>
            <div class="brick resource">
                <span class="brickIcon resourceIcon">
                    <span class="brickValue resourceValue">\${brick}</span>
                </span>
            </div>
            <div class="arrowGrey resource">
                <span class="arrowGreyIcon resourceIcon">
                    <span class="arrowGreyValue resourceValue">\${arrowGrey}</span>
                </span>
            </div>
            <div class="arrowRed resource">
                <span class="arrowRedIcon resourceIcon">
                    <span class="arrowRedValue resourceValue">\${arrowRed}</span>
                </span>
            </div>
            <div class="arrowBlue resource">
                <span class="arrowBlueIcon resourceIcon">
                    <span class="arrowBlueValue resourceValue">\${arrowBlue}</span>
                </span>
            </div>
            <div class="arrowUni resource">
                <span class="arrowUniIcon resourceIcon">
                    <span class="arrowUniValue resourceValue">\${arrowUni}</span>
                </span>
            </div>
            <div class="worker resource">
                <span class="workerIcon resourceIcon">
                    <span class="workerValue resourceValue">\${worker}</span>
                </span>
            </div>
            <div class="ammo resource">
                <span class="ammoIcon resourceIcon">
                    <span class="ammoValue resourceValue">\${ammo}</span>
                </span>
            </div>
            <div class="defence resource">
                <span class="defenceIcon resourceIcon">
                    <span class="defenceValue resourceValue">\${defence}</span>
                </span>
            </div>
            <div class="devel resource">
                <span class="develIcon resourceIcon">
                    <span class="develValue resourceValue">\${devel}</span>
                </span>
            </div>
        </div>
    </div>`;
</script>

{OVERALL_GAME_FOOTER}
