<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 *
 * @ApiResource(
 *      collectionOperations={"get", "post"},
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"event:read","event:item:get"}},
 *          },
 *          "put",
 *          "delete"
 *      },
 *      normalizationContext={"groups"={"event:read"}},
 *      denormalizationContext={"groups"={"event:write"}},
 *      attributes={
 *          "order"={"updatedAt": "DESC", "id": "ASC"}
 *      }
 * )
 */
class Event implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var UuidInterface The internal primary identity key
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({
     *     "event:read",
     *     "event:item:get"
     * })
     */
    protected $id;

    /**
     * @var string The label of the gift
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @Groups({
     *     "event:read",
     *     "event:write",
     *     "event:item:get"
     * })
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=false)
     *
     * @Assert\Length(max=4)
     *
     * @Groups({
     *     "event:read",
     *     "event:write",
     *     "event:item:get",
     * })
     */
    private $year;

    /**
     * @ORM\ManyToMany(targetEntity="Recipient")
     * @ORM\JoinTable(name="events_recipients",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recipient_id", referencedColumnName="id")}
     * )
     *
     * @Groups({
     *     "event:read",
     *     "event:write",
     *     "event:item:get"
     * })
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Recipient $participant): self
    {
        $this->participants->add($participant);

        return $this;
    }

    public function removeParticipant(Recipient $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
