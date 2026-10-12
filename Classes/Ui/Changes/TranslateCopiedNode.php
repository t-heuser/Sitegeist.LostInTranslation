<?php

declare(strict_types=1);

namespace Sitegeist\LostInTranslation\Ui\Changes;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Ui\ContentRepository\Service\NodeService;
use Neos\Neos\Ui\Domain\Model\AbstractChange;
use Neos\Neos\Ui\Domain\Model\Feedback\Messages\Error;
use Neos\Neos\Ui\Domain\Model\Feedback\Messages\Success;
use Neos\Neos\Ui\Domain\Model\Feedback\Operations\UpdateNodeInfo;
use Sitegeist\LostInTranslation\ContentRepository\NodeTranslationService;

/**
 * Translates a pasted node (the subject) from the node it was copied from, after the editor accepted the offer made by
 * the OfferTranslationOfCopiedNode feedback.
 */
class TranslateCopiedNode extends AbstractChange
{
    #[Flow\Inject]
    protected NodeTranslationService $nodeTranslationService;

    #[Flow\Inject]
    protected NodeService $nodeService;

    protected string $sourceContextPath = '';

    public function setSourceContextPath(string $sourceContextPath): void
    {
        $this->sourceContextPath = $sourceContextPath;
    }

    public function canApply()
    {
        return $this->getSourceNode() instanceof NodeInterface;
    }

    public function apply()
    {
        $sourceNode = $this->getSourceNode();
        if (!$sourceNode instanceof NodeInterface) {
            return;
        }

        $copiedNode = $this->getSubject();
        $failedNodes = $this->nodeTranslationService->translateCopiedNode($sourceNode, $copiedNode);
        if ($failedNodes === []) {
            $success = new Success();
            $success->setMessage('The pasted content was translated');
            $this->feedbackCollection->add($success);
        } else {
            $error = new Error();
            $error->setMessage(count($failedNodes) . ' of the pasted nodes could not be translated and still contain the original text');
            $this->feedbackCollection->add($error);
        }

        $updateNodeInfo = new UpdateNodeInfo();
        $updateNodeInfo->setNode($copiedNode);
        $updateNodeInfo->recursive();
        $this->feedbackCollection->add($updateNodeInfo);
        $this->updateWorkspaceInfo();
        $this->reloadDocument();
    }

    private function getSourceNode(): ?NodeInterface
    {
        if ($this->sourceContextPath === '') {
            return null;
        }
        $sourceNode = $this->nodeService->getNodeFromContextPath($this->sourceContextPath);
        return $sourceNode instanceof NodeInterface ? $sourceNode : null;
    }
}
