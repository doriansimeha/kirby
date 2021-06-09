<?php

use Kirby\Cms\Page;
use Kirby\Form\Form;

/**
 * Page
 */
return [
    'fields' => [
        'blueprint' => function (Page $page) {
            return $page->blueprint();
        },
        'blueprints' => function (Page $page) {
            return $page->blueprints();
        },
        'children' => function (Page $page) {
            return $page->children();
        },
        'content' => function (Page $page) {
            return Form::for($page)->values();
        },
        'drafts' => function (Page $page) {
            return $page->drafts();
        },
        'errors' => function (Page $page) {
            return $page->errors();
        },
        'files' => function (Page $page) {
            return $page->files()->sort('sort', 'asc', 'filename', 'asc');
        },
        'hasChildren' => function (Page $page) {
            return $page->hasChildren();
        },
        'hasDrafts' => function (Page $page) {
            return $page->hasDrafts();
        },
        'hasFiles' => function (Page $page) {
            return $page->hasFiles();
        },
        'id' => function (Page $page) {
            return $page->id();
        },
        'isSortable' => function (Page $page) {
            return $page->isSortable();
        },
        'next' => function (Page $page) {
            return $page->nextNavigation();
        },
        'num' => function (Page $page) {
            return $page->num();
        },
        'options' => function (Page $page) {
            return $page->panel()->options(['preview']);
        },
        'panelIcon' => function (Page $page) {
            return $page->panel()->icon();
        },
        'panelImage' => function (Page $page) {
            return $page->panel()->image();
        },
        'parent' => function (Page $page) {
            return $page->parent();
        },
        'parents' => function (Page $page) {
            return $page->parents()->flip();
        },
        'prev' => function (Page $page) {
            return $page->prevNavigation();
        },
        'previewUrl' => function (Page $page) {
            return $page->previewUrl();
        },
        'siblings' => function (Page $page) {
            if ($page->isDraft() === true) {
                return $page->parentModel()->children()->not($page);
            } else {
                return $page->siblings();
            }
        },
        'slug' => function (Page $page) {
            return $page->slug();
        },
        'status' => function (Page $page) {
            return $page->status();
        },
        'template' => function (Page $page) {
            return $page->intendedTemplate()->name();
        },
        'title' => function (Page $page) {
            return $page->title()->value();
        },
        'url' => function (Page $page) {
            return $page->url();
        },
    ],
    'type' => 'Kirby\Cms\Page',
    'views' => [
        'compact' => [
            'id',
            'title',
            'url',
            'num'
        ],
        'default' => [
            'content',
            'id',
            'status',
            'num',
            'options',
            'parent' => 'compact',
            'slug',
            'template',
            'title',
            'url'
        ],
        'panel' => [
            'id',
            'blueprint',
            'content',
            'status',
            'options',
            'next'    => ['id', 'slug', 'title'],
            'parents' => ['id', 'slug', 'title'],
            'prev'    => ['id', 'slug', 'title'],
            'previewUrl',
            'slug',
            'title',
            'url'
        ],
        'selector' => [
            'id',
            'title',
            'parent' => [
                'id',
                'title'
            ],
            'children' => [
                'hasChildren',
                'id',
                'panelIcon',
                'panelImage',
                'title',
            ],
        ]
    ],
];
