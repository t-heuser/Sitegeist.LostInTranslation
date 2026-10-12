<?php

declare(strict_types=1);

namespace Sitegeist\LostInTranslation\Ui\Feedback;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Neos\Ui\Domain\Model\AbstractFeedback;
use Neos\Neos\Ui\Domain\Model\FeedbackInterface;

/**
 * Asks the editor whether a node that was just pasted into another language should be translated.
 */
class OfferTranslationOfCopiedNode extends AbstractFeedback
{
    public function __construct(
        private readonly NodeInterface $sourceNode,
        private readonly NodeInterface $copiedNode,
        private readonly string $sourceLanguageLabel,
        private readonly string $targetLanguageLabel,
    ) {
    }

    public function getType()
    {
        return 'Sitegeist.LostInTranslation:OfferTranslationOfCopiedNode';
    }

    public function getDescription()
    {
        return sprintf('Node "%s" was pasted into another language and can be translated.', $this->copiedNode->getLabel());
    }

    public function isSimilarTo(FeedbackInterface $feedback)
    {
        return $feedback instanceof self
            && $feedback->copiedNode->getContextPath() === $this->copiedNode->getContextPath();
    }

    /**
     * @return array<string,string>
     */
    public function serializePayload(ControllerContext $controllerContext)
    {
        return [
            'sourceContextPath' => $this->sourceNode->getContextPath(),
            'copiedContextPath' => $this->copiedNode->getContextPath(),
            'sourceLanguageLabel' => $this->sourceLanguageLabel,
            'targetLanguageLabel' => $this->targetLanguageLabel,
        ];
    }
}
