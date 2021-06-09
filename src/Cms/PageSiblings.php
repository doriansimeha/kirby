<?php

namespace Kirby\Cms;

/**
 * PageSiblings
 *
 * @package   Kirby Cms
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://getkirby.com/license
 */
trait PageSiblings
{
    /**
     * Checks if there's a next listed
     * page in the siblings collection
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return bool
     */
    public function hasNextListed($collection = null): bool
    {
        return $this->nextListed($collection) !== null;
    }

    /**
     * Checks if there's a next unlisted
     * page in the siblings collection
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return bool
     */
    public function hasNextUnlisted($collection = null): bool
    {
        return $this->nextUnlisted($collection) !== null;
    }

    /**
     * Checks if there's a previous listed
     * page in the siblings collection
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return bool
     */
    public function hasPrevListed($collection = null): bool
    {
        return $this->prevListed($collection) !== null;
    }

    /**
     * Checks if there's a previous unlisted
     * page in the siblings collection
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return bool
     */
    public function hasPrevUnlisted($collection = null): bool
    {
        return $this->prevUnlisted($collection) !== null;
    }

    /**
     * Returns the next listed page if it exists
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return \Kirby\Cms\Page|null
     */
    public function nextListed($collection = null)
    {
        return $this->nextAll($collection)->listed()->first();
    }

    /**
     * Returns the next unlisted page if it exists
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return \Kirby\Cms\Page|null
     */
    public function nextUnlisted($collection = null)
    {
        return $this->nextAll($collection)->unlisted()->first();
    }

    /**
     * Returns the previous listed page
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return \Kirby\Cms\Page|null
     */
    public function prevListed($collection = null)
    {
        return $this->prevAll($collection)->listed()->last();
    }

    /**
     * Returns the previous unlisted page
     *
     * @param \Kirby\Cms\Collection|null $collection
     *
     * @return \Kirby\Cms\Page|null
     */
    public function prevUnlisted($collection = null)
    {
        return $this->prevAll($collection)->unlisted()->first();
    }

    /**
     * Private siblings collector
     *
     * @return \Kirby\Cms\Collection
     */
    protected function siblingsCollection()
    {
        if ($this->isDraft() === true) {
            return $this->parentModel()->drafts();
        } else {
            return $this->parentModel()->children();
        }
    }

    /**
     * Returns siblings with the same template
     *
     * @param bool $self
     * @return \Kirby\Cms\Pages
     */
    public function templateSiblings(bool $self = true)
    {
        return $this->siblings($self)->filter('intendedTemplate', $this->intendedTemplate()->name());
    }

    /**
     * Returns the next page in defined navigation
     *
     * @return \Kirby\Cms\Collection
     */
    public function nextNavigation()
    {
        return $this->filterNavigation($this->nextAll($this->siblingsNavigation()))->first();
    }

    /**
     * Returns the prev page in defined navigation
     *
     * @return \Kirby\Cms\Collection
     */
    public function prevNavigation()
    {
        return $this->filterNavigation($this->prevAll($this->siblingsNavigation()))->last();
    }

    /**
     * Returns siblings of defined navigation
     *
     * @return \Kirby\Cms\Collection
     */
    protected function siblingsNavigation()
    {
        $navigation  = $this->blueprint()->navigation() ?? [];
        $sortBy = $navigation['sortBy'] ?? null;
        $status = $navigation['status'] ?? null;

        // if status is defined in navigation, all items in the collection are used (drafts, listed and unlisted)
        // otherwise it depends on the status of the page
        $collection = $status !== null ? $this->parentModel()->childrenAndDrafts() : $this->siblingsCollection();

        // sort the collection if custom sortBy defined in navigation
        // otherwise default sorting will apply
        if ($sortBy !== null) {
            return $collection->sort(...$collection::sortArgs($sortBy));
        }

        return $collection;
    }

    /**
     * Returns filtered siblings for defined navigation
     *
     * @param Collection $collection
     * @return \Kirby\Cms\Collection
     */
    protected function filterNavigation(Collection $collection)
    {
        $navigation = $this->blueprint()->navigation() ?? [];

        if (empty($navigation) === false) {
            $status   = $navigation['status'] ?? $this->status();
            $template = $navigation['template'] ?? $this->intendedTemplate();

            $statuses  = is_array($status) === true ? $status : [$status];
            $templates = is_array($template) === true ? $template : [$template];

            // do not filter if template navigation is all
            if (in_array('all', $templates) === false) {
                $collection = $collection->filter('intendedTemplate', 'in', $templates);
            }

            // do not filter if status navigation is all
            if (in_array('all', $statuses) === false) {
                $collection = $collection->filter('status', 'in', $statuses);
            }
        } else {
            $collection = $collection
                ->filter('intendedTemplate', $this->intendedTemplate())
                ->filter('status', $this->status());
        }

        return $collection->filter('isReadable', true);
    }
}
