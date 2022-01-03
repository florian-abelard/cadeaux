<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     normalizationContext={"groups"={"group:read"}},
 *     denormalizationContext={"groups"={"group:write"}},
 *     attributes={"order"={"label": "ASC"}},
 * )
 *
 * @ApiFilter(GroupFilter::class, arguments={
 *      "parameterName": "groups",
 *      "overrideDefaultGroups": "false",
 *      "whitelist": {"group:read", "group:read:members"},
 * })
 */
class Group
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
     *     "group:read",
     *     "group:read:item",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "group:read",
     *     "group:read:item",
     * })
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recipient", mappedBy="group")
     *
     * @Groups({
     *     "group:read:members",
     * })
     */
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Recipient $recipient): self
    {
        $this->members->add($recipient);

        return $this;
    }

    public function removeMember(Recipient $recipient): self
    {
        $this->members->removeElement($recipient);

        return $this;
    }
}
