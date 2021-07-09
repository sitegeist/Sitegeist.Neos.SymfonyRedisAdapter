<?php
declare(strict_types=1);

namespace Sitegeist\Neos\SymfonyRedisAdapter\Cache;

use Neos\Cache\Backend\AbstractBackend;
use Neos\Cache\Backend\PhpCapableBackendInterface;
use Neos\Cache\Backend\RequireOnceFromValueTrait;
use Neos\Cache\Backend\TaggableBackendInterface;
use Neos\Cache\Backend\WithStatusInterface;
use Neos\Error\Messages\Result;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\CacheItem;

class SymfonyRedisAdapterBackend extends AbstractBackend implements TaggableBackendInterface, PhpCapableBackendInterface, WithStatusInterface
{
    use RequireOnceFromValueTrait;

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var mixed[]
     */
    protected $options;

    /**
     * @var RedisTagAwareAdapter
     */
    protected $adapter;

    /**
     * @var boolean|null
     */
    protected $frozen;

    /**
     * @param string $dsn
     */
    public function setDsn(string $dsn): void
    {
        $this->dsn = $dsn;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options = []): void
    {
        $this->options = $options;
    }

    /**
     * @return RedisTagAwareAdapter
     */
    protected function getAdapter(): RedisTagAwareAdapter
    {
        if (!$this->adapter) {
            $connection = RedisTagAwareAdapter::createConnection(
                $this->dsn,
                $this->options,
            );
            $this->adapter = new RedisTagAwareAdapter(
                $connection,
                $this->cacheIdentifier,
                $this->defaultLifetime
            );
        }
        return $this->adapter;
    }

    /**
     * @param string $entryIdentifier
     * @param string $data
     * @param array $tags
     * @param int|null $lifetime
     * @throws \Psr\Cache\CacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function set(string $entryIdentifier, string $data, array $tags = [], int $lifetime = null): void
    {
        $adapter = $this->getAdapter();
        /**
         * @var CacheItem $item
         */
        $item = $adapter->getItem($entryIdentifier);
        $item->set($data);
        $item->expiresAfter($lifetime);
        $item->tag($tags);
        $adapter->save($item);
    }

    /**
     * @param string $entryIdentifier
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(string $entryIdentifier)
    {
        $adapter = $this->getAdapter();
        /**
         * @var CacheItem $item
         */
        $item = $adapter->getItem($entryIdentifier);
        if ($item->isHit()) {
            return $item->get();
        } else {
            return false;
        }
    }

    /**
     * @param string $entryIdentifier
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function has(string $entryIdentifier): bool
    {
        $adapter = $this->getAdapter();
        return $adapter->hasItem($entryIdentifier);
    }

    /**
     * @param string $entryIdentifier
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function remove(string $entryIdentifier): bool
    {
        $adapter = $this->getAdapter();
        $adapter->delete($entryIdentifier);
    }

    /**
     *
     */
    public function flush(): void
    {
        $adapter = $this->getAdapter();
        $adapter->clear();
    }

    /**
     * This backend does not need an externally triggered garbage collection
     *
     * @return void
     * @api
     */
    public function collectGarbage(): void
    {
    }

    /**
     * @param string $tag
     * @return int
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function flushByTag(string $tag): int
    {
        $adapter = $this->getAdapter();
        $adapter->invalidateTags([$tag]);
    }


    /**
     * @param string $tag
     * @return array
     */
    public function findIdentifiersByTag(string $tag): array
    {
        $adapter = $this->getAdapter();
        return [];
    }

    /**
     * @return Result
     * @api
     */
    public function getStatus(): Result
    {
        $result = new Result();
        return $result;
    }


}
