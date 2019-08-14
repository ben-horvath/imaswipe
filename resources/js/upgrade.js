function upgrade(amount) {
    axios.post('/upgrade', {amount: amount});

    let action = '';

    if (amount > 0) {
        action = 'upgrade';
    } else {
        action = 'noUpgrade';
    }

    gtag('event', action, {
        'event_category': 'Upgrade',
        'value': amount
    });
}

export {
    upgrade
}
