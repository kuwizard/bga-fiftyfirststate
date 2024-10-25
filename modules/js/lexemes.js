define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
    return declare('state.lexemes', null, {
        placeText(tpl, text, destination, value = null) {
            dojo.place(this.format_block(tpl, { text: text, value: value }), destination);
        },

        getDeckLexeme() {
            return _('Deck');
        },

        getDiscardLexeme() {
            return _('Discard');
        },

        getExpandConnectionsLexeme() {
            return _('Expand this to see Connections');
        },

        getExpandLocationsLexeme() {
            return _('Expand this to see locations to choose from');
        },

        getSelectorAllLexeme() {
            return _('All');
        },

        getSelectorLocationsLexeme() {
            return _('Locations');
        },

        getSelectorConnectionsLexeme() {
            return _('Connections');
        },

        getFeatureAreaLexeme() {
            return _('Do not discard {cardIcon} from your hand during the Cleanup phase.');
        },

        getWorkersActionLexeme() {
            return _(
                'Spend 2 {workerIcon} to gain 1 Resource or 1 {cardIcon}. This Action may be activated any number of times.');
        },

        getFactionActionLexeme(faction) {
            return [
                [
                    _('Spend 1 {ironIcon} to gain 1 {arrowGreyIcon}.'),
                    _('Spend 1 {gunIcon} to gain 2 {arrowRedIcon}.'),
                    _('Spend 2 {fuelIcon} to gain 2 {arrowBlueIcon}.')
                ],
                [
                    _('Spend 1 {brickIcon} and discard 1 {cardIcon} to gain 2 {arrowGreyIcon}.'),
                    _('Spend 1 {gunIcon} to gain 2 {arrowRedIcon}.'),
                    _('Spend 1 {fuelIcon} to gain 2 {arrowBlueIcon}.')
                ],
                [
                    _('Spend 2 {ironIcon} to gain 2 {arrowGreyIcon}.'),
                    _('Spend 1 {gunIcon} to gain 3 {arrowRedIcon}.'),
                    _('Spend 1 {fuelIcon} to gain 1 {arrowBlueIcon}.')
                ],
                [
                    _('Spend 2 {ironIcon} to gain 2 {arrowGreyIcon}.'),
                    _('Spend 1 {gunIcon} to gain 2 {arrowRedIcon}.'),
                    _('Spend 1 {fuelIcon} to gain 3 {arrowBlueIcon}.')
                ],
            ][faction];
        },

        getCardWarningLexeme() {
            return _(
                'As a result of this action you\'re going to get one or more Location card(s) and, therefore, Reset Turn option will be unavailable. Your turn will end after this action.');
        },

        getPassWarningLexeme() {
            return _(
                'After you pass, you will not be able to take any Actions for the round. Also other players will not interact with your Locations in any way.');
        },

        getIDontCareFactionChooseLexeme() {
            return _('Faction will be assigned randomly based on what your opponents chose as their last priority');
        },

        getLocationText(textArray, name) {
            if (Object.keys(textArray).length !== 5) {
                throw new Error(`Unexpected error: getLocationText textArray contains ${Object.keys(textArray).length} elements, expected 5`);
            }
            Object.keys(textArray).forEach(function (key) {
                textArray[key] = this.replaceWithResourceIcon(_(textArray[key]), true);
            }.bind(this));

            const hidden = textArray.bbonus === '' ? ' hidden' : '';
            const activatedHidden = textArray.mayBeActivated === '' ? ' hidden' : '';
            return this.format_block(
                'jstpl_location_text',
                { ...textArray, name: _(name).toUpperCase(), hidden: hidden, activatedHidden: activatedHidden }
            );
        },

        getConnectionText(textArray, name) {
            if (Object.keys(textArray).length !== 2) {
                throw new Error(`Unexpected error: getConnectionText textArray contains ${Object.keys(textArray).length} elements, expected 5`);
            }
            Object.keys(textArray).forEach(function (key) {
                textArray[key] = this.replaceWithResourceIcon(_(textArray[key]), true);
            }.bind(this));

            return this.format_block(
                'jstpl_connection_text',
                { ...textArray, name: _(name).toUpperCase() }
            );
        },

        getFactionChooserHeaderLexeme() {
            return _('Choose your faction preferences<br/>(1 is the most preferred, 4 is the least)');
        },

        getFactionChooserDisclaimerLexeme() {
            return _(
                'Please note that the chosen faction is not guaranteed. If multiple players choose the same faction, it will be assigned randomly.');
        },

        getFactionChooserLeftSideLexeme() {
            return _('Side 1');
        },

        getFactionChooserRightSideLexeme() {
            return _('Side 2');
        },

        getProductionHeaderLexeme() {
            return _('Production');
        },

        getActionsHeaderLexeme() {
            return _('Actions');
        },

        getPriorityHeaderLexeme() {
            return _('Priority:');
        },
    });
});
