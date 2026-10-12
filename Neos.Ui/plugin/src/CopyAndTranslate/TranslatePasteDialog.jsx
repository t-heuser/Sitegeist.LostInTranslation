import React, { useEffect, useState } from 'react'

import { neos } from '@neos-project/neos-ui-decorators'
import { Button, Dialog, Icon } from '@neos-project/react-ui-components'

import { registerTranslationDialog } from './translationRequest'

const TranslatePasteDialog = ({ i18nRegistry }) => {
    const [request, setRequest] = useState(null)

    useEffect(() => registerTranslationDialog(setRequest), [])

    if (request === null) {
        return null
    }

    const t = (id, fallback, params = {}) =>
        i18nRegistry.translate(id, fallback, params, 'Sitegeist.LostInTranslation', 'Main')
    const answer = (translate) => () => {
        setRequest(null)
        request.resolve(translate)
    }
    const params = { source: request.sourceLabel, target: request.targetLabel }

    return (
        <Dialog
            id="sitegeist-lostintranslation-TranslatePasteDialog"
            isOpen
            title={
                <div>
                    <Icon icon="language" />
                    <span style={{ marginLeft: '8px' }}>{t('pasteDialog.title', 'Translate the pasted content?')}</span>
                </div>
            }
            onRequestClose={answer(false)}
            actions={[
                <Button key="keep" style="lighter" hoverStyle="brand" onClick={answer(false)}>
                    {t('pasteDialog.keep', 'Keep original')}
                </Button>,
                <Button key="translate" style="success" hoverStyle="success" onClick={answer(true)}>
                    {t('pasteDialog.translate', 'Translate')}
                </Button>,
            ]}
        >
            <div style={{ padding: '16px' }}>
                {t(
                    'pasteDialog.description',
                    'The content was copied from "{source}" to "{target}". Should it be translated automatically?',
                    params
                )}
            </div>
        </Dialog>
    )
}

export default neos((globalRegistry) => ({
    i18nRegistry: globalRegistry.get('i18n'),
}))(TranslatePasteDialog)
