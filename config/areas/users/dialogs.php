<?php

use Kirby\Cms\Find;
use Kirby\Cms\UserRules;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Panel\Field;

return [

    // create
    'users/create' => [
        'load' => function () {
            $kirby = kirby();
            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'name'  => Field::username(),
                        'email' => Field::email([
                            'link'     => false,
                            'required' => true
                        ]),
                        'password' => Field::password(),
                        'language' => Field::translation(),
                        'role'     => Field::role()
                    ],
                    'submitButton' => t('create'),
                    'value' => [
                        'name'     => '',
                        'email'    => '',
                        'password' => '',
                        'language' => $kirby->panelLanguage(),
                        'role'     => $kirby->user()->role()->name()
                    ]
                ]
            ];
        },
        'submit' => function () {
            kirby()->users()->create(get());
            return [
                'event' => 'user.create'
            ];
        }
    ],

    // change email
    'users/(:any)/changeEmail' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'email' => [
                            'label'     => t('email'),
                            'required'  => true,
                            'type'      => 'email',
                            'preselect' => true
                        ]
                    ],
                    'submitButton' => t('change'),
                    'value' => [
                        'email' => $user->email()
                    ]
                ]
            ];
        },
        'submit' => function (string $id) {
            Find::user($id)->changeEmail(get('email'));

            return [
                'event'    => 'user.changeEmail',
                'dispatch' => [
                    'content/revert' => ['users/' . $id]
                ]
            ];
        }
    ],

    // change language
    'users/(:any)/changeLanguage' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'language' => Field::translation()
                    ],
                    'submitButton' => t('change'),
                    'value' => [
                        'language' => $user->language()
                    ]
                ]
            ];
        },
        'submit' => function (string $id) {
            Find::user($id)->changeLanguage(get('language'));

            return [
                'event'  => 'user.changeLanguage',
                'reload' => [
                    'include' => '$translation'
                ]
            ];
        }
    ],

    // change name
    'users/(:any)/changeName' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'name' => Field::username([
                            'preselect' => true
                        ])
                    ],
                    'submitButton' => t('rename'),
                    'value' => [
                        'name' => $user->name()->value()
                    ]
                ]
            ];
        },
        'submit' => function (string $id) {
            Find::user($id)->changeName(get('name'));

            return [
                'event' => 'user.changeName'
            ];
        }
    ],

    // change password
    'users/(:any)/changePassword' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'password' => Field::password([
                            'label' => t('user.changePassword.new'),
                        ]),
                        'passwordConfirmation' => Field::password([
                            'label' => t('user.changePassword.new.confirm'),
                        ])
                    ],
                    'submitButton' => t('change'),
                ]
            ];
        },
        'submit' => function (string $id) {
            $user                 = Find::user($id);
            $password             = get('password');
            $passwordConfirmation = get('passwordConfirmation');

            // validate the password
            UserRules::validPassword($user, $password);

            // compare passwords
            if ($password !== $passwordConfirmation) {
                throw new InvalidArgumentException([
                    'key' => 'user.password.notSame'
                ]);
            }

            // change password if everything's fine
            $user->changePassword($password);

            return [
                'event' => 'user.changePassword'
            ];
        }
    ],

    // change role
    'users/(:any)/changeRole' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'role' => Field::role([
                            'label' => t('user.changeRole.select'),
                            'type'  => 'radio'
                        ])
                    ],
                    'submitButton' => t('user.changeRole'),
                    'value' => [
                        'role' => $user->role()->name()
                    ]
                ]
            ];
        },
        'submit' => function (string $id) {
            $user = Find::user($id)->changeRole(get('role'));

            return [
                'event' => 'user.changeRole',
                'user' => $user->toArray()
            ];
        }
    ],

    // delete
    'users/(:any)/delete' => [
        'load' => function (string $id) {
            $user = Find::user($id);

            return [
                'component' => 'k-remove-dialog',
                'props' => [
                    'text' => tt('user.delete.confirm', [
                        'email' => $user->email()
                    ])
                ]
            ];
        },
        'submit' => function (string $id) {
            Find::user($id)->delete();

            return [
                'event'    => 'user.delete',
                'dispatch' => [
                    'content/remove' => ['users/' . $id]
                ]
            ];
        }
    ],

];
