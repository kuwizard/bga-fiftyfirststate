define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.htmltemplates', null, {
        tplInfoPanel() {
            return `
    <div class='player-board' id="info-panel">
        <div class="info-panel-row" id="turn-counter-wrapper">
            ${_('Round')} <span id="turnNumber"></span>
        </div>
    </div>
   `;
        },
    });
});
