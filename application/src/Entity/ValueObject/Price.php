<?php

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 */
final class Price
{
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Assert\PositiveOrZero
     *
     * @Groups({
     *     "idea:write",
     *     "idea:read:item",
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $value;

    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?float
    {
        return $this->value;
    }

    public function equals(Price $price): bool
    {
        return $this->value() === $price->value();
    }
}
