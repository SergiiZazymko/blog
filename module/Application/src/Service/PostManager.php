<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 16:52
 */

namespace Application\Service;

use Application\Entity\Comment;
use Application\Entity\Post;
use Application\Entity\Tag;
use Application\Repository\PostRepository;
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removePost(Post $post)
    {
        /** @var ArrayCollection $comments */
        $comments = $post->getComments();

        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $this->dem->remove($comment);
        }

        /** @var ArrayCollection $tags */
        $tags = $post->getTags();

        foreach ($tags as $tag) {
            $post->removeTagAssociation($tags);
        }

        $this->dem->remove($post);

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
     * @param Post $post
     * @return string
     */
    public function getCommentCountStr(Post $post)
    {
        /** @var int $commentCount */
        $commentCount = count($post->getComments());
        if ($commentCount == 0)
            return 'No comments';
        else if ($commentCount == 1)
            return '1 comment';
        else
            return $commentCount . ' comments';
    }

    /**
     * @param Post $post
     * @param array $data
     */
    public function addCommentToPost(Post $post, array $data)
    {
        /** @var Comment $comment */
        $comment = new Comment;
        $comment->exchangeArray($data);
        $comment->setContent($data['comment']);
        $comment->setPost($post);
        $comment->setDateCreated(new \DateTime());

        $this->dem->persist($comment);
        $this->dem->flush();
    }

    /**
     * @param $post
     * @return string
     */
    public function getPostStatusAsString($post)
    {
        switch ($post->getStatus()) {
            case Post::STATUS_DRAFT: return 'Draft';
            case Post::STATUS_PUBLISHED: return 'Published';
        }

        return 'Unknown';
    }

    /**
     * @return array
     */
    public function getTagCloud()
    {
        /** @var array $tagCloud */
        $tagCloud = [];

        /** @var PostRepository $postRepository */
        $postRepository = $this->dem->getRepository(Post::class);

        /** @var int $totalPostsCount */
        $totalPostsCount = count($postRepository->findAllHavingAnyTag());

        /** @var array $tags */
        $tags = $this->dem
            ->getRepository(Tag::class)
            ->findAll();

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            /** @var array $postsByTag */
            $postsByTag = $postRepository->findAllByTagName($tag->getName());

            if ($postsCount = count($postsByTag)) {
                $tagCloud[$tag->getName()] = $postsCount;
            }
        }

        /** @var array $normalizedTagCloud */
        $normalizedTagCloud = [];

        /**
         * @var string $tag
         * @var int $count
         */
        foreach ($tagCloud as $tag => $count) {
            $normalizedTagCloud[$tag] = $count / $totalPostsCount;
        }

        return $normalizedTagCloud;
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