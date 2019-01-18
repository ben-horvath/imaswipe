function exists(unknown) {
    if (
        typeof unknown == 'undefined' ||
        unknown === null
    ) {
        return false;
    } else {
        return true;
    }
}

export {
    exists
}
