<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipientRepository")
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     normalizationContext={"groups"={"recipient:read"}},
 *     denormalizationContext={"groups"={"recipient:write"}},
 *     attributes={
 *         "order"={"name": "ASC"},
 *         "pagination_enabled"=false
 *     },
 * )
 */
class Recipient
{
    /**
     * @var UuidInterface The internal primary identity key
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({
     *     "recipient:read",
     *     "idea:read",
     *     "idea:read:item",
     *     "gift:read",
     *     "event:read:item",
     *     "group:read:members",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "recipient:read",
     *     "idea:read",
     *     "idea:read:item",
     *     "gift:read",
     *     "event:read:item",
     *     "group:read:members",
     * })
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="members")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *
     * @Groups({
     *     "idea:read",
     *     "gift:read",
     * })
     */
    private $group;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }
}
