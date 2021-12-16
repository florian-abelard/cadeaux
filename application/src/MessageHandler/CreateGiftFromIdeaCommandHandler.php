<?php

namespace App\MessageHandler;

use App\Dto\CreateGiftFromIdeaCommand;
use App\Entity\Gift;
use App\Entity\Idea;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGiftFromIdeaCommandHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateGiftFromIdeaCommand $command): Gift
    {
        $gift = $this->createGift(
            $command->getIdea(),
            $command->getEventYear(),
            $command->getRecipients(),
        );

        $this->udpateOriginIdea(
            $command->getIdea(),
            $command->getRecipients()
        );

        $this->entityManager->flush();

        return $gift;
    }

    private function createGift(Idea $idea, string $eventYear, array $recipients): Gift
    {
        $gift = new Gift();

        $gift->setLabel($idea->getLabel());
        $gift->setPrice($idea->getPrice());
        $gift->setNote($idea->getNote());
        $gift->setEventYear($eventYear);

        foreach ($recipients as $recipient) {
            $gift->addRecipient($recipient);
        }

        $this->entityManager->persist($gift);

        return $gift;
    }

    private function udpateOriginIdea(Idea $idea, array $giftRecipients): void
    {
        foreach ($giftRecipients as $recipient) {
            $idea->removeRecipient($recipient);
        }

        if (count($idea->getRecipients()) === 0) {
            $this->entityManager->remove($idea);
        } else {
            $this->entityManager->persist($idea);
        }
    }
}
