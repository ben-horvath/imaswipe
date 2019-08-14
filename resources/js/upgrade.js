function upgrade(amount) {
    axios.post('/upgrade', {amount: amount});

    let action = '';

    if (amount > 0) {
        action = 'upgrade';
    } else {
        action = 'noUpgrade';
    }

    ga('send', 'event', 'Upgrade', action, '', amount);
}

export {
    upgrade
}
