<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogCachePool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class FetchBlogPostFromCacheListenerFactory
{
    public function __invoke(ContainerInterface $container) : FetchBlogPostFromCacheListener
    {
        $config  = $container->get('config')['blog'] ?? [];

        return new FetchBlogPostFromCacheListener(
            $container->get(BlogCachePool::class),
            $config['cache']['enabled'] ?? false
        );
    }
}
