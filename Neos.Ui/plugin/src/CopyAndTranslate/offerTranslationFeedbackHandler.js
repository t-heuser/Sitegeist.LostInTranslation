import { actions } from '@neos-project/neos-ui-redux-store'

import { askForTranslation } from './translationRequest'

export const OFFER_TRANSLATION_FEEDBACK = 'Sitegeist.LostInTranslation:OfferTranslationOfCopiedNode'

/**
 * Pasting several nodes at once yields one offer per node in the same response. The handler runs once per offer, so
 * only the first one asks, and the answer applies to all of them.
 */
export function offerTranslationFeedbackHandler(payload, { store, allFeedbacksFromThisRequest }) {
    const offers = allFeedbacksFromThisRequest
        .filter((feedback) => feedback.type === OFFER_TRANSLATION_FEEDBACK)
        .map((feedback) => feedback.payload)
    if (offers[0] !== payload) {
        return
    }

    askForTranslation({
        sourceLabel: payload.sourceLanguageLabel,
        targetLabel: payload.targetLanguageLabel,
    }).then((translate) => {
        if (!translate) {
            return
        }
        store.dispatch(
            actions.Changes.persistChanges(
                offers.map((offer) => ({
                    type: 'Sitegeist.LostInTranslation:TranslateCopiedNode',
                    subject: offer.copiedContextPath,
                    payload: { sourceContextPath: offer.sourceContextPath },
                }))
            )
        )
    })
}
