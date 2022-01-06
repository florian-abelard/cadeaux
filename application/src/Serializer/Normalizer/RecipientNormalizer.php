<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Recipient;
use App\Repository\GiftRepository;
use App\Repository\IdeaRepository;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class RecipientNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'EVENT_NORMALIZER_ALREADY_CALLED';

    private IdeaRepository $ideaRepository;
    private GiftRepository $giftRepository;
    private IriConverterInterface $iriConverter;

    public function __construct(
        IdeaRepository $ideaRepository,
        GiftRepository $giftRepository,
        IriConverterInterface $iriConverter
    ) {
        $this->ideaRepository = $ideaRepository;
        $this->giftRepository = $giftRepository;
        $this->iriConverter = $iriConverter;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $context[static::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        if (in_array('event:read:item', $context['groups'])) {
            $event = $this->iriConverter->getItemFromIri($context['request_uri']);

            $data['ideas'] = $this
                ->ideaRepository
                ->findByRecipient($object)
            ;

            $data['gifts'] = $this
                ->giftRepository
                ->findByRecipientAndEvent($object, $event)
            ;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        // avoid recursion: only call once per object
        if (isset($context[static::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Recipient;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}
