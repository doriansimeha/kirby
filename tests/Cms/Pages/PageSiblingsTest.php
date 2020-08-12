<?php

namespace Kirby\Cms;

use PHPUnit\Framework\TestCase;

class PageSiblingsTest extends TestCase
{
    protected $app;
    protected $fixtures;

    public function setUp(): void
    {
        $this->app = new App([
            'roots' => [
                'index' => '/dev/null'
            ]
        ]);
    }

    protected function site($children = null)
    {
        $this->app = $this->app->clone([
            'site' => [
                'children' => $children ?? $this->collection(),
            ]
        ]);

        return $this->app->site();
    }

    protected function collection()
    {
        return [
            ['slug' => 'project-a'],
            ['slug' => 'project-b'],
            ['slug' => 'project-c']
        ];
    }

    public function testDefaultSiblings()
    {
        $page = new Page(['slug' => 'test']);
        $this->assertInstanceOf(Pages::class, $page->siblings());
    }

    public function testHasNext()
    {
        $children = $this->site()->children();

        $this->assertTrue($children->first()->hasNext());
        $this->assertFalse($children->last()->hasNext());
    }

    public function testHasNextCustomCollection()
    {
        $children = $this->site()->children();
        $page = $children->first();

        $this->assertTrue($page->hasNext());
        $this->assertFalse($page->hasNext($children->flip()));
    }

    public function testHasNextListed()
    {
        $site = $this->site([
            ['slug' => 'unlisted'],
            ['slug' => 'listed', 'num' => 1],
        ]);

        $collection = $site->children();

        $this->assertTrue($collection->first()->hasNextListed());
        $this->assertFalse($collection->last()->hasNextListed());
    }

    public function testHasNextUnlisted()
    {
        $site = $this->site([
            ['slug' => 'listed', 'num' => 1],
            ['slug' => 'unlisted'],
        ]);

        $collection = $site->children();

        $this->assertTrue($collection->first()->hasNextUnlisted());
        $this->assertFalse($collection->last()->hasNextUnlisted());
    }

    public function testHasPrev()
    {
        $collection = $this->site()->children();

        $this->assertTrue($collection->last()->hasPrev());
        $this->assertFalse($collection->first()->hasPrev());
    }

    public function testHasPrevCustomCollection()
    {
        $children = $this->site()->children();
        $page = $children->last();

        $this->assertTrue($page->hasPrev());
        $this->assertFalse($page->hasPrev($children->flip()));
    }

    public function testHasPrevListed()
    {
        $site = $this->site([
            ['slug' => 'listed', 'num' => 1],
            ['slug' => 'unlisted'],
        ]);

        $collection = $site->children();

        $this->assertFalse($collection->first()->hasPrevListed());
        $this->assertTrue($collection->last()->hasPrevListed());
    }

    public function testHasPrevUnlisted()
    {
        $site = $this->site([
            ['slug' => 'unlisted'],
            ['slug' => 'listed', 'num' => 1]
        ]);

        $collection = $site->children();

        $this->assertFalse($collection->first()->hasPrevUnlisted());
        $this->assertTrue($collection->last()->hasPrevUnlisted());
    }

    public function testIndexOf()
    {
        $collection = $this->site()->children();

        $this->assertEquals(0, $collection->first()->indexOf());
        $this->assertEquals(1, $collection->nth(1)->indexOf());
        $this->assertEquals(2, $collection->last()->indexOf());
    }

    public function testIndexOfCustomCollection()
    {
        $collection = $this->site()->children();
        $page = $collection->first();

        $this->assertEquals(0, $page->indexOf());
        $this->assertEquals(2, $page->indexOf($collection->flip()));
    }

    public function testIsFirst()
    {
        $collection = $this->site()->children();

        $this->assertTrue($collection->first()->isFirst());
        $this->assertFalse($collection->last()->isFirst());
    }

    public function testIsLast()
    {
        $collection = $this->site()->children();

        $this->assertTrue($collection->last()->isLast());
        $this->assertFalse($collection->first()->isLast());
    }

    public function testIsNth()
    {
        $collection = $this->site()->children();

        $this->assertTrue($collection->first()->isNth(0));
        $this->assertTrue($collection->nth(1)->isNth(1));
        $this->assertTrue($collection->last()->isNth($collection->count() - 1));
    }

    public function testNext()
    {
        $collection = $this->site()->children();

        $this->assertEquals($collection->first()->next(), $collection->nth(1));
    }

    public function testNextAll()
    {
        $collection = $this->site()->children();
        $first      = $collection->first();

        $this->assertCount(2, $first->nextAll());

        $this->assertEquals($first->nextAll()->first(), $collection->nth(1));
        $this->assertEquals($first->nextAll()->last(), $collection->nth(2));
    }

    public function testNextListed()
    {
        $collection = $this->site([
            ['slug' => 'unlisted-a'],
            ['slug' => 'unlisted-b'],
            ['slug' => 'listed', 'num' => 1],
        ])->children();

        $this->assertEquals('listed', $collection->first()->nextListed()->slug());
    }

    public function testNextUnlisted()
    {
        $collection = $this->site([
            ['slug' => 'listed-a', 'num' => 1],
            ['slug' => 'listed-b', 'num' => 2],
            ['slug' => 'unlisted'],
        ])->children();

        $this->assertEquals('unlisted', $collection->first()->nextUnlisted()->slug());
    }

    public function testPrev()
    {
        $collection = $this->site()->children();

        $this->assertEquals($collection->last()->prev(), $collection->nth(1));
    }

    public function testPrevAll()
    {
        $collection = $this->site()->children();
        $last       = $collection->last();

        $this->assertCount(2, $last->prevAll());

        $this->assertEquals($last->prevAll()->first(), $collection->nth(0));
        $this->assertEquals($last->prevAll()->last(), $collection->nth(1));
    }

    public function testPrevListed()
    {
        $collection = $this->site([
            ['slug' => 'listed', 'num' => 1],
            ['slug' => 'unlisted-a'],
            ['slug' => 'unlisted-b'],
        ])->children();

        $this->assertEquals('listed', $collection->last()->prevListed()->slug());
    }

    public function testPrevUnlisted()
    {
        $collection = $this->site([
            ['slug' => 'unlisted'],
            ['slug' => 'listed-a', 'num' => 1],
            ['slug' => 'listed-b', 'num' => 2],
        ])->children();

        $this->assertEquals('unlisted', $collection->last()->prevUnlisted()->slug());
    }

    public function testSiblings()
    {
        $site     = $this->site();
        $page     = $site->children()->nth(1);
        $children = $site->children();
        $siblings = $children->not($page);

        $this->assertEquals($children, $page->siblings());
        $this->assertEquals($siblings, $page->siblings(false));
    }

    public function testDraftSiblings()
    {
        $parent = new Page([
            'slug' => 'parent',
            'children' => [
                ['slug' => 'a'],
                ['slug' => 'b'],
            ],
            'drafts' => [
                ['slug' => 'c'],
                ['slug' => 'd'],
                ['slug' => 'e'],
            ]
        ]);

        $drafts = $parent->drafts();
        $draft  = $drafts->find('c');

        $this->assertEquals($drafts, $draft->siblings());
    }

    public function testTemplateSiblings()
    {
        $site = $this->site([
            [
                'slug'     => 'a',
                'template' => 'project'
            ],
            [
                'slug'     => 'b',
                'template' => 'article'
            ],
            [
                'slug'     => 'c',
                'template' => 'project'
            ],
            [
                'slug'     => 'd',
                'template' => 'project'
            ]
        ]);

        $pages    = $site->children();
        $siblings = $pages->first()->templateSiblings();

        $this->assertTrue($siblings->has('a'));
        $this->assertTrue($siblings->has('c'));
        $this->assertTrue($siblings->has('d'));

        $this->assertFalse($siblings->has('b'));

        $siblings = $pages->first()->templateSiblings(false);

        $this->assertTrue($siblings->has('c'));
        $this->assertTrue($siblings->has('d'));

        $this->assertFalse($siblings->has('a'));
        $this->assertFalse($siblings->has('b'));
    }

    public function testCycleOne()
    {
        $app = new App([
            'roots' => [
                'index' => $this->fixtures = __DIR__ . '/fixtures/PageSiblingsTest'
            ],
            'blueprints' => [
                'pages/a' => [
                    'title' => 'A',
                    'cycle' => [
                        'status' => 'all',
                        'template' => 'all'
                    ]
                ],
                'pages/b' => [
                    'title' => 'B',
                    'cycle' => [
                        'status' => 'all',
                        'template' => 'all'
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $page = Page::create([
            'slug' => 'test'
        ]);

        $page->createChild([
            'slug'     => 'a',
            'template' => 'a'
        ]);

        $expectedPrevPage = $page->createChild([
            'slug'     => 'b',
            'template' => 'b'
        ]);

        $testPage = $page->createChild([
            'slug'     => 'c',
            'template' => 'a'
        ]);

        $expectedNextPage = $page->createChild([
            'slug'     => 'd',
            'template' => 'b'
        ]);

        $page->createChild([
            'slug'     => 'e',
            'template' => 'a'
        ]);

        $page->createChild([
            'slug'     => 'f',
            'template' => 'b'
        ]);

        $cycleOption = $testPage->blueprint()->cycle();
        $this->assertSame(['status' => 'all', 'template' => 'all'], $cycleOption);
        $this->assertSame($expectedPrevPage, $testPage->prevCycle($cycleOption));
        $this->assertSame($expectedNextPage, $testPage->nextCycle($cycleOption));
    }

    public function testCycleTwo()
    {
        $app = new App([
            'roots' => [
                'index' => $this->fixtures = __DIR__ . '/fixtures/PageSiblingsTest'
            ],
            'blueprints' => [
                'pages/c' => [
                    'title' => 'C',
                    'cycle' => [
                        'status' => ['listed'],
                        'template' => ['c']
                    ]
                ],
                'pages/d' => [
                    'title' => 'D',
                    'cycle' => [
                        'status' => ['listed'],
                        'template' => ['c']
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $page = Page::create([
            'slug' => 'test'
        ]);

        $expectedPrevPage = $page->createChild([
            'slug'     => 'a',
            'template' => 'c'
        ])->changeStatus('listed');

        $page->createChild([
            'slug'     => 'b',
            'template' => 'd'
        ])->changeStatus('listed');

        $page->createChild([
            'slug'     => 'c',
            'template' => 'c'
        ]);

        $testPage = $page->createChild([
            'slug'     => 'd',
            'template' => 'd'
        ])->changeStatus('listed');

        $expectedNextPage = $page->createChild([
            'slug'     => 'e',
            'template' => 'c'
        ])->changeStatus('listed');

        $page->createChild([
            'slug'     => 'f',
            'template' => 'd'
        ])->changeStatus('listed');

        $cycleOption = $testPage->blueprint()->cycle();

        $this->assertSame([
            'status' => ['listed'],
            'template' => ['c']
        ], $cycleOption);
        $this->assertSame($expectedPrevPage, $testPage->prevCycle($cycleOption));
        $this->assertSame($expectedNextPage, $testPage->nextCycle($cycleOption));
    }

    public function testCycleThree()
    {
        $app = new App([
            'roots' => [
                'index' => $this->fixtures = __DIR__ . '/fixtures/PageSiblingsTest'
            ],
            'blueprints' => [
                'pages/e' => [
                    'title' => 'E',
                    'cycle' => [
                        'status' => ['listed'],
                        'template' => ['e', 'f']
                    ]
                ],
                'pages/f' => [
                    'title' => 'F',
                    'cycle' => [
                        'status' => ['listed'],
                        'template' => ['e', 'f']
                    ]
                ]
            ]
        ]);

        $app->impersonate('kirby');

        $page = Page::create([
            'slug' => 'test'
        ]);

        $expectedPrevPage = $page->createChild([
            'slug'     => 'a',
            'template' => 'e'
        ])->changeStatus('listed');

        $page->createChild([
            'slug'     => 'b',
            'template' => 'f'
        ])->changeStatus('unlisted');

        $page->createChild([
            'slug'     => 'c',
            'template' => 'e'
        ])->changeStatus('unlisted');

        $testPage = $page->createChild([
            'slug'     => 'd',
            'template' => 'f'
        ])->changeStatus('listed');

        $page->createChild([
            'slug'     => 'e',
            'template' => 'e'
        ])->changeStatus('unlisted');

        $expectedNextPage = $page->createChild([
            'slug'     => 'f',
            'template' => 'f'
        ])->changeStatus('listed');

        $cycleOption = $testPage->blueprint()->cycle();

        $this->assertSame([
            'status' => ['listed'],
            'template' => ['e', 'f']
        ], $cycleOption);
        $this->assertSame($expectedPrevPage, $testPage->prevCycle($cycleOption));
        $this->assertSame($expectedNextPage, $testPage->nextCycle($cycleOption));
    }

    public function tearDown(): void
    {
        Dir::remove($this->fixtures . '/content');
    }
}
