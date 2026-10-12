import manifest from '@neos-project/neos-ui-extensibility'
import { registerRetranslateView } from '@sitegeist/lostintranslation-retranslate-editor'

import { registerCopyAndTranslate } from './CopyAndTranslate'

manifest('@sitegeist/lostintranslation', {}, (globalRegistry) => {
    registerRetranslateView(globalRegistry)
    registerCopyAndTranslate(globalRegistry)
})
