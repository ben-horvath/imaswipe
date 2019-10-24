function upgrade(amount) {
    axios.post('/upgrade', {amount: amount});

    let action = '';

    if (amount > 0) {
        action = 'upgrade';
    } else {
        action = 'noUpgrade';
    }

    if (typeof gtag === 'function') { /* check if Google tracking is enabled */
        gtag('event', action, {
            'event_category': 'Upgrade',
            'value': amount
        });
    }

    if (typeof fbq === 'function') { /* check if Google tracking is enabled */
        fbq('track', 'InitiateCheckout', {
            value: amount,
            currency: 'USD',
        });
    }
}

export {
    upgrade
}
