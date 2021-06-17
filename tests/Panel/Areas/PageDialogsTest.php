<?php

namespace Kirby\Panel\Areas;

class PageDialogsTest extends AreaTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->install();
        $this->login();
    }

    public function testChangeSort(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeSort');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('Please select a position', $props['fields']['position']['label']);
        $this->assertSame('Change', $props['submitButton']);
        $this->assertSame(1, $props['value']['position']);
    }

    public function testChangeSortOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([
            'status' => 'listed'
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeSort');

        $this->assertSame('page.sort', $dialog['event']);
        $this->assertSame(200, $dialog['code']);

        $this->assertSame('listed', $this->app->page('test')->status());
        $this->assertSame(1, $this->app->page('test')->num());
    }

    public function testChangeStatus(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeStatus');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('Select a new status', $props['fields']['status']['label']);

        $this->assertSame('Draft', $props['fields']['status']['options'][0]['text']);
        $this->assertSame('Unlisted', $props['fields']['status']['options'][1]['text']);
        $this->assertSame('Public', $props['fields']['status']['options'][2]['text']);

        $this->assertSame('Please select a position', $props['fields']['position']['label']);
        $this->assertSame(['status' => 'listed'], $props['fields']['position']['when']);

        $this->assertSame('Change', $props['submitButton']);

        $this->assertSame('unlisted', $props['value']['status']);
        $this->assertSame(1, $props['value']['position']);
    }

    public function testChangeStatusOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([
            'status' => 'listed'
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeStatus');

        $this->assertSame('page.changeStatus', $dialog['event']);
        $this->assertSame(200, $dialog['code']);

        $this->assertSame('listed', $this->app->page('test')->status());
        $this->assertSame(1, $this->app->page('test')->num());
    }

    public function testChangeTemplate(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug'     => 'test',
                        'template' => 'a'
                    ]
                ]
            ],
            'blueprints' => [
                'pages/a' => [
                    'title' => 'A',
                    'options' => [
                        'changeTemplate' => [
                            'b'
                        ]
                    ]
                ],
                'pages/b' => [
                    'title' => 'B',
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeTemplate');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('Template', $props['fields']['template']['label']);

        $this->assertSame('A', $props['fields']['template']['options'][0]['text']);
        $this->assertSame('a', $props['fields']['template']['options'][0]['value']);
        $this->assertSame('B', $props['fields']['template']['options'][1]['text']);
        $this->assertSame('b', $props['fields']['template']['options'][1]['value']);

        $this->assertSame('Change', $props['submitButton']);
        $this->assertSame('a', $props['value']['template']);
    }

    public function testChangeTemplateWithoutAlternatives(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug'     => 'test',
                        'template' => 'a'
                    ]
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeTemplate');

        $this->assertSame(500, $dialog['code']);
        $this->assertSame('The template for the page "test" cannot be changed', $dialog['error']);
    }

    public function testChangeTemplateOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug'     => 'test',
                        'template' => 'a'
                    ]
                ]
            ],
            'blueprints' => [
                'pages/a' => [
                    'title' => 'A',
                    'options' => [
                        'changeTemplate' => [
                            'b'
                        ]
                    ]
                ],
                'pages/b' => [
                    'title' => 'B',
                ]
            ]
        ]);

        $this->submit([
            'template' => 'b'
        ]);

        $this->login();

        // store page first to be able to change the template
        $this->app->page('test')->update();

        $dialog = $this->dialog('pages/test/changeTemplate');

        $this->assertSame('page.changeTemplate', $dialog['event']);
        $this->assertSame(200, $dialog['code']);

        $this->assertSame('b', $this->app->page('test')->intendedTemplate()->name());
    }

    public function testChangeTitle(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('Title', $props['fields']['title']['label']);
        $this->assertFalse($props['fields']['title']['disabled']);
        $this->assertSame('URL appendix', $props['fields']['slug']['label']);
        $this->assertFalse($props['fields']['slug']['disabled']);

        $this->assertSame('test', $props['value']['title']);
        $this->assertSame('test', $props['value']['slug']);

        $this->assertSame('Change', $props['submitButton']);
    }

    public function testChangeTitleOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([
            'title' => 'New title',
            'slug' => 'test'
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(['page.changeTitle'], $dialog['event']);
        $this->assertSame(200, $dialog['code']);

        $this->assertSame('New title', $this->app->page('test')->title()->value());
    }

    public function testChangeTitleOnSubmitWithoutChanges(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([
            'title' => 'test',
            'slug'  => 'test'
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(200, $dialog['code']);
        $this->assertArrayNotHasKey('event', $dialog);
    }

    public function testChangeTitleOnSubmitWithoutTitle(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([]);
        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(400, $dialog['code']);
        $this->assertSame('The title must not be empty', $dialog['error']);
    }

    public function testChangeTitleOnSubmitWithoutSlug(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit(['title' => 'Test']);
        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(400, $dialog['code']);
        $this->assertSame('Please enter a valid URL appendix', $dialog['error']);
    }

    public function testChangeTitleOnSubmitWithSlugOnly(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit(['title' => 'test', 'slug' => 'new-slug']);
        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(['page.changeSlug'], $dialog['event']);
        $this->assertSame(200, $dialog['code']);
        $this->assertSame([
            'content/move' => [
                '/pages/test',
                '/pages/new-slug'
            ]
        ], $dialog['dispatch']);

        $this->assertSame('new-slug', $this->app->page('new-slug')->slug());
    }

    public function testChangeTitleOnSubmitWithSlugAndTitle(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit(['title' => 'New title', 'slug' => 'new-slug']);
        $this->login();

        $dialog = $this->dialog('pages/test/changeTitle');

        $this->assertSame(['page.changeTitle', 'page.changeSlug'], $dialog['event']);
        $this->assertSame(200, $dialog['code']);
        $this->assertSame([
            'content/move' => [
                '/pages/test',
                '/pages/new-slug'
            ]
        ], $dialog['dispatch']);

        $this->assertSame('New title', $this->app->page('new-slug')->title()->value());
        $this->assertSame('new-slug', $this->app->page('new-slug')->slug());
    }

    public function testCreate(): void
    {
        $dialog = $this->dialog('pages/create');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('hidden', $props['fields']['parent']['type']);
        $this->assertSame('Title', $props['fields']['title']['label']);
        $this->assertSame('URL appendix', $props['fields']['slug']['label']);
        $this->assertSame('title', $props['fields']['slug']['sync']);

        // there's only the default template for now
        $this->assertArrayNotHasKey('template', $props['fields']);

        $this->assertSame('Create draft', $props['submitButton']);

        $this->assertSame('site', $props['value']['parent']);
        $this->assertSame('', $props['value']['slug']);
        $this->assertNull($props['value']['template']);
        $this->assertSame('', $props['value']['title']);
    }

    public function testCreateWithParent(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ],
            'request' => [
                'query' => [
                    'parent' => 'pages/test'
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/create');
        $props  = $dialog['props'];

        $this->assertSame('pages/test', $props['value']['parent']);
    }

    public function testCreateWithMultipleBlueprints(): void
    {
        $this->app([
            'blueprints' => [
                'pages/a' => ['title' => 'A'],
                'pages/b' => ['title' => 'B'],
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/create');
        $props  = $dialog['props'];

        // a + b + default
        $this->assertCount(3, $props['fields']['template']['options']);
    }

    public function testCreateOnSubmit(): void
    {
        $this->submit([
            'title' => 'Test',
            'slug'  => 'test'
        ]);

        $dialog = $this->dialog('pages/create');

        $this->assertSame('page.create', $dialog['event']);
        $this->assertSame(200, $dialog['code']);

        $this->assertSame('test', $this->app->page('test')->slug());
        $this->assertSame('Test', $this->app->page('test')->title()->value());
    }

    public function testCreateOnSubmitWithParent(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ],
            'request' => [
                'query' => [
                    'parent' => 'pages/test'
                ]
            ]
        ]);

        $this->submit([
            'title' => 'Test',
            'slug'  => 'test-child'
        ]);

        $dialog = $this->dialog('pages/create');

        $this->assertSame('test-child', $this->app->page('test/test-child')->slug());
        $this->assertSame('Test', $this->app->page('test/test-child')->title()->value());
    }

    public function testCreateOnSubmitWithoutTitle(): void
    {
        $this->submit([
            'slug' => 'test'
        ]);

        $dialog = $this->dialog('pages/create');

        $this->assertSame(400, $dialog['code']);
        $this->assertSame('The title must not be empty', $dialog['error']);
    }

    public function testDelete(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/delete');
        $props  = $dialog['props'];

        $this->assertRemoveDialog($dialog);
        $this->assertSame('Do you really want to delete <strong>test</strong>?', $props['text']);
    }

    public function testDeleteWithChildren(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'children' => [
                            ['slug' => 'test-child']
                        ]
                    ]
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/delete');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);
        $this->assertSame('info', $props['fields']['info']['type']);
        $this->assertSame('text', $props['fields']['check']['type']);
        $this->assertSame('Do you really want to delete <strong>test</strong>?', $props['text']);
        $this->assertSame('Delete', $props['submitButton']);
        $this->assertSame('medium', $props['size']);
    }

    public function testDeleteOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->submit([]);
        $this->login();

        $dialog = $this->dialog('pages/test/delete');

        $this->assertSame('page.delete', $dialog['event']);
        $this->assertSame(200, $dialog['code']);
        $this->assertCount(0, $this->app->site()->children());
    }

    public function testDeleteOnSubmitWithChildrenWithoutCheck(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'children' => [
                            ['slug' => 'test-child']
                        ]
                    ]
                ]
            ]
        ]);

        $this->submit([]);
        $this->login();

        $dialog = $this->dialog('pages/test/delete');

        $this->assertSame(400, $dialog['code']);
        $this->assertSame('Please enter the page title to confirm', $dialog['error']);
    }

    public function testDeleteOnSubmitWithChildren(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'children' => [
                            ['slug' => 'test-child']
                        ]
                    ]
                ]
            ]
        ]);

        $this->submit(['check' => 'test']);
        $this->login();

        $dialog = $this->dialog('pages/test/delete');

        $this->assertSame('page.delete', $dialog['event']);
        $this->assertSame(200, $dialog['code']);
        $this->assertCount(0, $this->app->site()->children());
    }

    public function testDuplicate(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/duplicate');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('URL appendix', $props['fields']['slug']['label']);
        $this->assertSame('slug', $props['fields']['slug']['type']);
        $this->assertSame('/', $props['fields']['slug']['path']);

        $this->assertSame('Duplicate', $props['submitButton']);

        $this->assertFalse($props['value']['children']);
        $this->assertFalse($props['value']['files']);
        $this->assertSame('test-copy', $props['value']['slug']);
    }

    public function testDuplicateWithChildren(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'children' => [
                            ['slug' => 'test-child']
                        ]
                    ]
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/duplicate');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('toggle', $props['fields']['children']['type']);
        $this->assertSame('Copy pages', $props['fields']['children']['label']);
        $this->assertSame('1/1', $props['fields']['children']['width']);
    }

    public function testDuplicateWithFiles(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'files' => [
                            ['filename' => 'test.jpg']
                        ]
                    ]
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/duplicate');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('toggle', $props['fields']['files']['type']);
        $this->assertSame('Copy files', $props['fields']['files']['label']);
        $this->assertSame('1/1', $props['fields']['files']['width']);
    }

    public function testDuplicateWithChildrenAndFiles(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'children' => [
                            ['slug' => 'test-child']
                        ],
                        'files' => [
                            ['filename' => 'test.jpg']
                        ]
                    ]
                ]
            ]
        ]);

        $this->login();

        $dialog = $this->dialog('pages/test/duplicate');
        $props  = $dialog['props'];

        $this->assertFormDialog($dialog);

        $this->assertSame('toggle', $props['fields']['children']['type']);
        $this->assertSame('Copy pages', $props['fields']['children']['label']);
        $this->assertSame('1/2', $props['fields']['children']['width']);

        $this->assertSame('toggle', $props['fields']['files']['type']);
        $this->assertSame('Copy files', $props['fields']['files']['label']);
        $this->assertSame('1/2', $props['fields']['files']['width']);
    }

    public function testDuplicateOnSubmit(): void
    {
        $this->app([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ]
        ]);

        $this->login();

        // store the dummy page on disk
        // otherwise it cannot be duplicated
        $this->app->page('test')->update();

        $this->submit([
            'slug' => 'new-test'
        ]);

        $dialog = $this->dialog('pages/test/duplicate');

        $this->assertSame('page.duplicate', $dialog['event']);
        $this->assertSame('/pages/new-test', $dialog['redirect']);
        $this->assertSame(200, $dialog['code']);

        $this->assertCount(1, $this->app->site()->drafts());
    }
}
