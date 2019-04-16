function upgrade(amount) {
    axios.post('/upgrade', {amount: amount});
}

export {
    upgrade
}
