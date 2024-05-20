<?php
declare(strict_types=1);

namespace App\Model\Domain;

use App\Database\TagRepository;

class TagDomainService
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param string[] $texts
     * @return Tag[]
     */
    public function findOrCreateTags(array $texts): array
    {
        $texts = array_values(array_unique($texts));

        $tags = $this->tagRepository->findTags($texts);
        if (count($texts) === count($tags))
        {
            return $tags;
        }

        $existingTexts = array_map(static fn(Tag $tag) => $tag->getText(), $tags);
        $newTexts = array_values(array_diff($texts, $existingTexts));
        $newTags = $this->createTags($newTexts);

        return array_merge($tags, $newTags);
    }

    /**
     * NOTE: Данный метод не обрабатывает ситуацию, когда несколько транзакций одновременно пытаются создать одинаковый тег.
     *
     * @param string[] $texts
     * @return Tag[]
     */
    private function createTags(array $texts): array
    {
        $tags = [];
        foreach ($texts as $text)
        {
            $tag = new Tag($text);
            $tags[] = $tag;
            $this->tagRepository->add($tag);
        }
        $this->tagRepository->flush();

        return $tags;
    }
}
