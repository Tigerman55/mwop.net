<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Github;

use Laminas\Feed\Reader\Reader as FeedReader;

use function sprintf;

class AtomReader
{
    private const ATOM_FORMAT = 'https://github.com/%s.atom';

    protected $filters = [];
    protected $limit   = 10;
    protected $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }

    public function read(): array
    {
        $url  = sprintf(self::ATOM_FORMAT, $this->user);
        $feed = FeedReader::import($url);

        $entries = AtomCollection::make($feed)
            ->filterChain($this->filters)
            ->slice(0, $this->limit)
            ->map(function ($entry) {
                return [
                    'title' => $entry->getTitle(),
                    'link'  => $entry->getLink(),
                ];
            });

        return [
            'last_modified' => $feed->getDateModified(),
            'link'          => $feed->getLink(),
            'links'         => $entries->toArray(),
        ];
    }
}
