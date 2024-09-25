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
        <div class="card resource">
            <span class="cardIcon resourceIcon">
                <span class="cardValue resourceValue">\${handAmount}</span>
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
    var jstpl_board = `<div id="board"></div>`
    var jstpl_faction_board = `
    <div id="faction_\${id}" class="factionBoard">
        <div class="factionWrapper">
            <div class="deals"></div>
            <div class="faction" data-faction="\${faction}">
                <div id="featureArea_\${id}" class="featureArea"></div>
                <div class="spentArea">
                    <div class="spent" data-order="0"></div>
                    <div class="spent" data-order="1"></div>
                    <div class="spent" data-order="2"></div>
                </div>
                <div id="actionsArea_\${id}" class="actionsArea"></div>
                <div id="spendWorkersArea_\${id}" class="spendWorkersArea"></div>
            </div>
        </div>
        <div class="cards">
            <div class="production cardsBlock"></div>
            <div class="feature cardsBlock"></div>
            <div class="actions cardsBlock"></div>
        </div>
    </div>
    `;
    var jstpl_location = `<div class="locationWrapper location\${additionalClass}" id="location_\${id}">
        <div class="locationImage" data-sprite="\${sprite}">
            <div class="resources"></div>
        </div>
    </div>`
    var jstpl_connection = `<div id="connection_\${id}" class="connection\${additionalClass}" data-sprite="\${sprite}"></div>`
    var jstpl_hand = `<div id="hand" class="all">
            <div id="handLocations"></div>
            <div id="handConnections"></div>
        </div>`
    var jstpl_deck_connections = `<div id="deckConnectionsBlock">
        <div id="deckDiscard">
            <div id="deckBlock">
                <div id="deckHeader"></div>
                <div id="deck"></div>
            </div>
            <div id="discardBlock">
                <div id="discardHeader"></div>
                <div id="discard"></div>
            </div>
        </div>
        <div id="connections"></div>
        <div id="lookout" class="hidden"></div>
        <div id="collapseButton">
            <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 330 330" xml:space="preserve">
                <path id="XMLID_222_" d="M250.606,154.389l-150-149.996c-5.857-5.858-15.355-5.858-21.213,0.001
            c-5.857,5.858-5.857,15.355,0.001,21.213l139.393,139.39L79.393,304.394c-5.857,5.858-5.857,15.355,0.001,21.213
            C82.322,328.536,86.161,330,90,330s7.678-1.464,10.607-4.394l149.999-150.004c2.814-2.813,4.394-6.628,4.394-10.606
            C255,161.018,253.42,157.202,250.606,154.389z"/>
            </svg>
        </div>
    </div>`
    var jstpl_header = `<span class="header">
        <span class="headerText">\${text}</span>
        <span class="headerValue">\${value}</span>
    </span>`
    var jstpl_resource_icon = `<span class="\${type}Icon resourceIcon"></span>`
    var jstpl_resource_icon_log = `<span class="\${type}Icon resourceIcon logIcon"></span>`
    var jstpl_resource_block = `<span class="\${type}Block resourceBlock"></span>`
    var jstpl_last_round = `<div id="lastRound">\${text}</div>`
    var jstpl_collapsed_text = '<div class="collapsedText">\${text}</div>'
    var jstpl_selector = `<div id="handSelector">
            <div id="unselected">
                <div class="allBlock"></div>
                <div class="locationsBlock"></div>
                <div class="connectionsBlock"></div>
            </div>
            <div id="selected">
                <div class="allBlock"></div>
                <div class="locationsBlock"></div>
                <div class="connectionsBlock"></div>
            </div>
            <div id="clickArea">
                <div class="allBlock"></div>
                <div class="locationsBlock"></div>
                <div class="connectionsBlock"></div>
            </div>
        </div>`
</script>

{OVERALL_GAME_FOOTER}
