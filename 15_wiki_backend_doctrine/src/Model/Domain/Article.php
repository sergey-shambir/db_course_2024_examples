<?php
declare(strict_types=1);

namespace App\Model\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'article')]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    // NOTE: Version используется для оптимистичной блокировки
    #[ORM\Column(type: 'integer')]
    #[ORM\Version]
    private int $version;

    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'string', length: 2 ** 16 - 1, nullable: false)]
    private string $content;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable', nullable: false)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'created_by', type: 'integer', nullable: false)]
    private int $createdBy;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'updated_by', type: 'integer')]
    private ?int $updatedBy = null;

    /**
     * @var Collection<Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'article_tag')]
    #[ORM\JoinColumn(name: 'article_id')]
    #[ORM\InverseJoinColumn(name: 'tag_id')]
    private Collection $tags;

    /**
     * @param string $title
     * @param string $content
     * @param Tag[] $tags
     * @param int $createdBy
     */
    public function __construct(
        string $title,
        string $content,
        array $tags,
        int $createdBy
    )
    {
        $this->title = $title;
        $this->content = $content;
        $this->tags = new ArrayCollection(array_values($tags));
        $this->createdAt = new \DateTimeImmutable();
        $this->createdBy = $createdBy;
    }

    /**
     * @param int $userId
     * @param string $title
     * @param string $content
     * @param Tag[] $tags
     * @return void
     */
    public function edit(int $userId, string $title, string $content, array $tags): void
    {
        $this->title = $title;
        $this->content = $content;
        $this->editTags($tags);
        $this->updatedAt = new \DateTimeImmutable();
        $this->updatedBy = $userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags->toArray();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    /**
     * @param Tag[] $tags
     * @return void
     */
    private function editTags(array $tags): void
    {
        $oldTagsMap = self::getTagsByIdMap($this->tags->toArray());
        $newTagsMap = self::getTagsByIdMap($tags);

        $tagsToBeRemoved = array_values(array_diff_key($oldTagsMap, $newTagsMap));
        $tagsToBeAdded = array_values(array_diff_key($newTagsMap, $oldTagsMap));

        foreach ($tagsToBeRemoved as $tag)
        {
            $this->tags->removeElement($tag);
        }
        foreach ($tagsToBeAdded as $tag)
        {
            $this->tags->add($tag);
        }
    }

    /**
     * @param Tag[] $tags
     * @return array<string,Tag>
     */
    private static function getTagsByIdMap(array $tags): array
    {
        $map = [];
        foreach ($tags as $tag)
        {
            $map[$tag->getId()] = $tag;
        }
        return $map;
    }
}
