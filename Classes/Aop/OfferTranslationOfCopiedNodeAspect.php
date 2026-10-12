<?php

declare(strict_types=1);

namespace Sitegeist\LostInTranslation\Aop;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Neos\Ui\Domain\Model\Changes\AbstractCopy;
use Neos\Neos\Ui\Domain\Model\FeedbackCollection;
use Sitegeist\LostInTranslation\ContentRepository\NodeTranslationService;
use Sitegeist\LostInTranslation\Ui\Feedback\OfferTranslationOfCopiedNode;

/**
 * When a Neos UI copy lands in another language, the UI is told so it can offer to translate the copy.
 *
 * finish() is the one place every copy change (into, before, after) passes with the finished copy.
 */
#[Flow\Aspect]
class OfferTranslationOfCopiedNodeAspect
{
    #[Flow\Inject]
    protected NodeTranslationService $nodeTranslationService;

    #[Flow\Inject]
    protected FeedbackCollection $feedbackCollection;

    #[Flow\InjectConfiguration('nodeTranslation.languageDimensionName')]
    protected string $languageDimensionName;

    /**
     * @var array<string,array{presets: array<string,array{label?: string}>}>
     */
    #[Flow\InjectConfiguration('contentDimensions', 'Neos.ContentRepository')]
    protected array $contentDimensionConfiguration;

    #[Flow\AfterReturning('within(Neos\Neos\Ui\Domain\Model\Changes\AbstractCopy) && method(.*->finish())')]
    public function offerTranslationOfCopiedNode(JoinPointInterface $joinPoint): void
    {
        $change = $joinPoint->getProxy();
        $copiedNode = $joinPoint->getMethodArgument('node');
        if (!$change instanceof AbstractCopy || !$copiedNode instanceof NodeInterface) {
            return;
        }

        $sourceNode = $change->getSubject();
        if (!$this->nodeTranslationService->canTranslateCopiedNode($sourceNode, $copiedNode)) {
            return;
        }

        $this->feedbackCollection->add(new OfferTranslationOfCopiedNode(
            $sourceNode,
            $copiedNode,
            $this->getLanguageLabel($sourceNode),
            $this->getLanguageLabel($copiedNode)
        ));
    }

    private function getLanguageLabel(NodeInterface $node): string
    {
        $dimensionValue = (string) ($node->getContext()->getTargetDimensions()[$this->languageDimensionName] ?? '');
        return $this->contentDimensionConfiguration[$this->languageDimensionName]['presets'][$dimensionValue]['label'] ?? $dimensionValue;
    }
}
