import { OFFER_TRANSLATION_FEEDBACK, offerTranslationFeedbackHandler } from './offerTranslationFeedbackHandler'
import TranslatePasteDialog from './TranslatePasteDialog'

export function registerCopyAndTranslate(globalRegistry) {
    globalRegistry.get('serverFeedbackHandlers').set(`${OFFER_TRANSLATION_FEEDBACK}/Main`, offerTranslationFeedbackHandler)
    globalRegistry.get('containers').set('Modals/Sitegeist.LostInTranslation:TranslatePasteDialog', TranslatePasteDialog)
}
