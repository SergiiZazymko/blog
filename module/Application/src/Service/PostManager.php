<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 16:52
 */

namespace Application\Service;

use Application\Entity\Post;
use Application\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Zend\Filter\StaticFilter;
use Zend\Filter\StringTrim;

/**
 * Class PostManager
 * @package Application\Service
 */
class PostManager
{
    /** @var EntityManager $dem */
    protected $dem;

    /**
     * PostManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->dem = $entityManager;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function addNewPost(array $data)
    {
        /** @var Post $post */
        $post = new Post;

        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);
        $post->setDateCreated(new \DateTime());

        $this->addTagsToPost($data['tags'], $post);

        $this->dem->persist($post);
        $this->dem->flush();
    }

    /**
     * @param $post
     * @param $data
     */
    public function editPost(Post $post, array $data)
    {
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setStatus($data['status']);

        $this->addTagsToPost($data['tags'], $post);

        $this->dem->flush();
    }

    /**
     * @param Post $post
     * @return string
     */
    public function convertTagsToString(Post $post)
    {
        /** @var ArrayCollection $tags */
        $tags = $post->getTags();

        /** @var array $tagArray */
        $tagArray = [];

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $tagArray[] = $tag->getName();
        }

        return implode(', ', $tagArray);
    }

    /**
     * @param string $tagsStr
     * @param Post $post
     * @throws \Doctrine\ORM\ORMException
     */
    private function addTagsToPost(string $tagsStr, Post $post)
    {
        /** @var ArrayCollection $tags */
        $tags = $post->getTags();

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $post->removeTagAssociation($tag);
        }

        $tags = explode(', ', $tagsStr);

        /** @var string $tagName */
        foreach ($tags as $tagName) {
            $tagName = StaticFilter::execute($tagName, StringTrim::class);

            if (empty($tagName)) {
                continue;
            }

            /** @var Tag $tag */
            $tag = $this->dem->getRepository(Tag::class)
                ->findOneByName($tagName);

            if (!$tag) {
                $tag = new Tag;
                $tag->setName($tagName);
            }

            $this->dem->persist($tag);

            $post->addTag($tag);
        }
    }
}