let showDialog = null

export function registerTranslationDialog(show) {
    showDialog = show
    return () => {
        if (showDialog === show) {
            showDialog = null
        }
    }
}

/**
 * Resolves to true when the editor wants the pasted content translated.
 *
 * Without a mounted dialog there is nobody to ask, so the copy stays as it is.
 */
export function askForTranslation(request) {
    if (showDialog === null) {
        return Promise.resolve(false)
    }
    return new Promise((resolve) => showDialog({ ...request, resolve }))
}
