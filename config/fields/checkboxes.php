<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

return [
    'mixins' => ['min', 'options'],
    'props' => [
        /**
         * Unset inherited props
         */
        'after'       => null,
        'before'      => null,
        'icon'        => null,
        'placeholder' => null,

        /**
         * Arranges the checkboxes in the given number of columns
         */
        'columns' => function (int $columns = 1) {
            return $columns;
        },
        /**
         * Maximum number of checked boxes
         */
        'max' => function (int $max = null) {
            return $max;
        },
        /**
         * Minimum number of checked boxes
         */
        'min' => function (int $min = null) {
            return $min;
        },
        'value' => function ($value = null) {
            return Str::split($value, ',');
        },
    ],
    'computed' => [
        'default' => function () {
            $default = Str::split($this->toString($this->default), ',');

            return $this->sanitizeOptions($default);
        },
        'value' => function () {
            return $this->sanitizeOptions($this->value);
        },
    ],
    'save' => function ($value): string {
        return A::join($value, ', ');
    },
    'validations' => [
        'options',
        'max',
        'min'
    ]
];
