const hide = function (id) {
    document.getElementById(id).parentElement.classList.add('hidden');
}

const show = function (id) {
    document.getElementById(id).parentElement.classList.remove('hidden');
}

const hideAndShowFormElements = function () {
    const randomTeamForm = document.getElementById('random-team-form');
    if (randomTeamForm.length > 0) {
        const formData = new FormData(randomTeamForm);
        const mode = formData.get('tx_kickermanagerspiel_randomteam[randomDemand][mode]');

        switch (mode) {
            case 'classic':
                hide('formationselect');
                hide('players-per-club');
                hide('points-mode-select');
                hide('number-goalkeepers');
                hide('number-defenders');
                hide('number-midfielders');
                hide('number-forwards');
                break;
            case 'interactive':
                show('formationselect');
                hide('players-per-club');
                hide('points-mode-select');
                hide('number-goalkeepers');
                hide('number-defenders');
                hide('number-midfielders');
                hide('number-forwards');
                break;
            case 'custom':
                show('formationselect');
                show('players-per-club');
                show('points-mode-select');
                show('number-goalkeepers');
                show('number-defenders');
                show('number-midfielders');
                show('number-forwards');
                break;
        }
    }
};

const onRandomTeamFormChange = function () {
    const randomTeamForm = document.getElementById('random-team-form');
    if (randomTeamForm.length > 0) {
        randomTeamForm.addEventListener('input', function () {
            hideAndShowFormElements();
        });
    }
};

const onLeagueChange = function () {
    document.getElementById('league').addEventListener('change', function () {
        const randomTeamForm = document.getElementById('random-team-form');
        if (randomTeamForm.length > 0) {
            const formData = new FormData(randomTeamForm);
            const mode = formData.get('tx_kickermanagerspiel_randomteam[randomDemand][mode]');
            const minInvest = document.getElementById('min-invest');
            const maxInvest = document.getElementById('max-invest');
            switch (this.value) {
                case 2:
                case '2':
                    minInvest.step = 0.1;
                    switch (mode) {
                        case 'classic':
                            minInvest.value = 7.5;
                            maxInvest.value = 7.5;
                            minInvest.max = 7.5;
                            maxInvest.max = 7.5;
                            break;
                        case 'interactive':
                            minInvest.value = 10;
                            maxInvest.value = 10;
                            minInvest.max = 10;
                            maxInvest.max = 10;
                            break;
                        case 'custom':
                            minInvest.max = 99;
                            maxInvest.max = 99;
                    }
                    break;
                case 3:
                case '3':
                    minInvest.step = 0.05;
                    maxInvest.step = 0.05;
                    switch (mode) {
                        case 'classic':
                            minInvest.value = 4;
                            maxInvest.value = 4;
                            minInvest.max = 4;
                            maxInvest.max = 4;
                            break;
                        case 'interactive':
                            minInvest.value = 6;
                            maxInvest.value = 6;
                            minInvest.max = 6;
                            maxInvest.max = 6;
                            break;
                        case 'custom':
                            minInvest.max = 99;
                            maxInvest.max = 99;
                    }
                    break;
                default:
                    minInvest.step = 0.1;
                    switch (mode) {
                        case 'classic':
                            minInvest.value = 30;
                            maxInvest.value = 30;
                            minInvest.max = 30;
                            maxInvest.max = 30;
                            break;
                        case 'interactive':
                            minInvest.value = 42.5;
                            maxInvest.value = 42.5;
                            minInvest.max = 42.5;
                            maxInvest.max = 42.5;
                            break;
                        case 'custom':
                            minInvest.max = 99;
                            maxInvest.max = 99;
                    }
                    break;
            }
        }
    });
};

const onModeChange = function () {
    document.getElementById('mode').addEventListener('change', function () {
        document.getElementById('formationselect').dispatchEvent(new Event('change'));
        document.getElementById('league').dispatchEvent(new Event('change'));
        let cheapGoalkeepers = document.getElementById('number-cheap-goalkeepers');
        let cheapDefenders = document.getElementById('number-cheap-defenders');
        let cheapMidfielders = document.getElementById('number-cheap-midfielders');
        let cheapForwards = document.getElementById('number-cheap-forwards');
        switch (this.value) {
            case 'classic':
                cheapGoalkeepers.max = 2;
                cheapDefenders.max = 4;
                cheapMidfielders.max = 6;
                cheapForwards.max = 3;
                break;
            default:
                cheapGoalkeepers.max = 3;
                cheapDefenders.max = 6;
                cheapMidfielders.max = 8;
                cheapForwards.max = 5;
                break;
        }
    });
};

const onFormationChange = function () {
    document.getElementById('formationselect').addEventListener('change', function () {
        const randomTeamForm = document.getElementById('random-team-form');
        if (randomTeamForm.length > 0) {
            const formData = new FormData(randomTeamForm);
            const mode = formData.get('tx_kickermanagerspiel_randomteam[randomDemand][mode]');
            if (mode === 'classic') {
                this.value = '352';
            }
            const formationArray = this.value.split('');
            const bench = document.getElementById('bench');
            if (bench.classList.value !== '') {
                const formation = document.getElementById('formation');
                formation.removeAttribute('class');
                formation.classList.add('formation-' + formationArray[0] + formationArray[1] + formationArray[2]);
                bench.removeAttribute('class');
                bench.classList.add('bench-' + this.value);
            }
        }
    });
};

hideAndShowFormElements();
onRandomTeamFormChange();
onLeagueChange();
onModeChange();
onFormationChange();
