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
        <div class="cardsAndStuff">
            <div class="card resource">
                <span class="cardIcon resourceIcon">
                    <span class="cardValue resourceValue">\${card}</span>
                </span>
            </div>
            <div class="firstPlayerWrapper"></div>
        </div>
        <div class="playerResources">
            <div class="iron resource">
                <span class="ironIcon resourceIcon">
                    <span class="ironValue resourceValue">\${iron}</span>
                </span>
            </div>
            <div class="gun resource">
                <span class="gunIcon resourceIcon">
                    <span class="gunValue resourceValue">\${gun}</span>
                </span>
            </div>
            <div class="fuel resource">
                <span class="fuelIcon resourceIcon">
                    <span class="fuelValue resourceValue">\${fuel}</span>
                </span>
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
    var jstpl_board = `<div id="board"><div id="toTop"></div></div>`
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
        <div class="nameCardsWrapper">
            <div class="name"></div>
            <div class="cards">
                <div class="production cardsBlock"></div>
                <div class="feature cardsBlock"></div>
                <div class="actions cardsBlock"></div>
            </div>
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
        <div id="connections"><div id="connectionsCards"></div></div>
        <div id="lookout" class="hidden"></div>
        <div id="collapseButton">
        </div>
    </div>`
    var jstpl_arrow_up = `
            <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 330 330" xml:space="preserve">
                <path id="XMLID_222_" d="M250.606,154.389l-150-149.996c-5.857-5.858-15.355-5.858-21.213,0.001
                c-5.857,5.858-5.857,15.355,0.001,21.213l139.393,139.39L79.393,304.394c-5.857,5.858-5.857,15.355,0.001,21.213
                C82.322,328.536,86.161,330,90,330s7.678-1.464,10.607-4.394l149.999-150.004c2.814-2.813,4.394-6.628,4.394-10.606
                C255,161.018,253.42,157.202,250.606,154.389z"/>
            </svg>`
    var jstpl_header = `<span class="header">
        <span class="headerText">\${text}</span>
        <span class="headerValue">\${value}</span>
    </span>`
    var jstpl_resource_icon = `<span class="\${type}Icon resourceIcon"></span>`
    var jstpl_resource_icon_log = `<span class="\${type}Icon resourceIcon logIcon"></span>`
    var jstpl_resource_acon_log = `<span class="\${type}Acon resourceAcon logAcon"></span>`
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
    var jstpl_location_text = `<div class="locationNameText">\${name}</div>
    <b>\${type}:</b> \${description}.<span class="mayBeActivated \${activatedHidden}">
        \${mayBeActivated}
    </span>
    <br/>
    <div class="buildingBonus\${hidden}">
        <b>\${bbonus}:</b> \${bonusDescription}.
    </div>`
    var jstpl_connection_text = `<div class="locationNameText">\${name}</div>
    <b>\${type}:</b> \${description}.`
    var jstpl_first_player = '<div id="firstPlayer"></div>'
    var jstpl_faction_chooser = `<div id="factionChooser">
        <div id="chooserHeader">\${header}</div>
        <div id="chooserDisclaimer">\${disclaimer}</div>
        <div id="factions"></div>
    </div>`
    var jstpl_faction_to_choose = `<div class="factionToChoose" data-faction="\${type}">
        <div class="factionName"></div>
        <div class="productionHeader">\${productionHeader}</div>
        <div class="production">\${production}</div>
        <div class="actionsHeader">\${actionsHeader}</div>
        <div class="actions">\${actions}</div>
        <div class="priority">
            <span class="priorityHeader">\${priorityHeader}</span>
            <span class="priorityDropdownWrapper">
                <select id="priorityDropdown_\${type}" class="priorityDropdown">
                    <option value="0">Any</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </span>
        </div>
        <div class="sideChooser">
            <span id="factionInfo_\${altType}" class="factionInfo" data-type="\${altType}">?</span>
            <span class="optionOne">\${optionOne}</span>
            <input type="checkbox" id="switch_\${type}" class="switch" checked/><label for="switch_\${type}">Toggle</label>
            <span class="optionTwo">\${optionTwo}</span>
            <span id="factionInfo_\${type}" class="factionInfo" data-type="\${type}">?</span>
        </div>
    </div>`
    var jstpl_midsize_dialog = `<div class="midSizeDialog">\${content}</div>`
    var jstpl_keyhole = `
    <div id="scroll_to_\${pId}" class="keyhole">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 85 145">
            <path fill="currentColor" d="M 1.6,144.19321 C 0.72,143.31321 0,141.90343 0,141.06039 0,140.21734 5.019,125.35234 11.15333,108.02704 L 22.30665,76.526514 14.626511,68.826524 C 8.70498,62.889705 6.45637,59.468243 4.80652,53.884537 0.057,37.810464 3.28288,23.775161 14.266011,12.727735 23.2699,3.6711383 31.24961,0.09115725 42.633001,0.00129225 c 15.633879,-0.123414 29.7242,8.60107205 36.66277,22.70098475 8.00349,16.263927 4.02641,36.419057 -9.54327,48.363567 l -6.09937,5.36888 10.8401,30.526466 c 5.96206,16.78955 10.84011,32.03102 10.84011,33.86992 0,1.8389 -0.94908,3.70766 -2.10905,4.15278 -1.15998,0.44513 -19.63998,0.80932 -41.06667,0.80932 -28.52259,0 -39.386191,-0.42858 -40.557621,-1.6 z M 58.000011,54.483815 c 3.66666,-1.775301 9.06666,-5.706124 11.99999,-8.735161 l 5.33334,-5.507342 -6.66667,-6.09345 C 59.791321,26.035633 53.218971,23.191944 43.2618,23.15582 33.50202,23.12041 24.44122,27.164681 16.83985,34.94919 c -4.926849,5.045548 -5.023849,5.323672 -2.956989,8.478106 3.741259,5.709878 15.032709,12.667218 24.11715,14.860013 4.67992,1.129637 13.130429,-0.477436 20,-3.803494 z m -22.33337,-2.130758 c -2.8907,-1.683676 -6.3333,-8.148479 -6.3333,-11.893186 0,-11.58942 14.57544,-17.629692 22.76923,-9.435897 8.41012,8.410121 2.7035,22.821681 -9,22.728685 -2.80641,-0.0223 -6.15258,-0.652121 -7.43593,-1.399602 z m 14.6667,-6.075289 c 3.72801,-4.100734 3.78941,-7.121364 0.23656,-11.638085 -2.025061,-2.574448 -3.9845,-3.513145 -7.33333,-3.513145 -10.93129,0 -13.70837,13.126529 -3.90323,18.44946 3.50764,1.904196 7.30574,0.765377 11,-3.29823 z m -11.36999,0.106494 c -3.74071,-2.620092 -4.07008,-7.297494 -0.44716,-6.350078 3.2022,0.837394 4.87543,-1.760912 2.76868,-4.29939 -1.34051,-1.615208 -1.02878,-1.94159 1.85447,-1.94159 4.67573,0 8.31873,5.36324 6.2582,9.213366 -1.21644,2.27295 -5.30653,5.453301 -7.0132,5.453301 -0.25171,0 -1.79115,-0.934022 -3.42099,-2.075605 z"></path>
        </svg>
    </div>`
</script>

{OVERALL_GAME_FOOTER}
