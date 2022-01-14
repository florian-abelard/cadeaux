<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\ValueObject\Price;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GiftRepository")
 *
 * @ApiResource(
 *      collectionOperations={"get", "post"},
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"gift:read","gift:read:item"}},
 *          },
 *          "put",
 *          "delete"
 *      },
 *      normalizationContext={"groups"={"gift:read"}},
 *      denormalizationContext={"groups"={"gift:write"}},
 *      attributes={
 *          "order"={"updatedAt": "DESC", "id": "ASC"}
 *      }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={
 *      "label": "ipartial",
 *      "recipients.group.id": "exact",
 *      "recipients.id": "exact",
 *      "eventYear": "exact"
 * })
 */
class Gift implements TimestampableInterface
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
     *     "gift:read",
     *     "gift:read:item",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "gift:read",
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $label;

    /**
     * @var Price The price of the gift
     *
     * @ORM\Embedded(class="App\Entity\ValueObject\Price")
     *
     * @Assert\Type(type="App\Entity\ValueObject\Price")
     *
     * @Groups({
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="Recipient")
     * @ORM\JoinTable(name="gifts_recipients",
     *      joinColumns={@ORM\JoinColumn(name="gift_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recipient_id", referencedColumnName="id")}
     * )
     *
     * @Groups({
     *     "gift:read",
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $recipients;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     *
     * @Assert\Length(max=4)
     *
     * @Groups({
     *     "gift:read",
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $eventYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({
     *     "gift:write",
     *     "gift:read:item",
     * })
     */
    private $note;

    public function __construct()
    {
        $this->price = new Price();
        $this->recipients = new ArrayCollection();
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

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(Recipient $recipient): self
    {
        $this->recipients->add($recipient);

        return $this;
    }

    public function removeRecipient(Recipient $recipient): self
    {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    public function getEventYear(): ?string
    {
        return $this->eventYear;
    }

    public function setEventYear(?string $eventYear): self
    {
        $this->eventYear = $eventYear;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
