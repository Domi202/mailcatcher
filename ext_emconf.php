<?php
/***************************************************************
 * Extension Manager/Repository config file for ext "mailcatcher".
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'mailcatcher',
    'description' => 'Catches mails and displays them in TYPO3 backend',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '0.1.0',
    'state' => 'stable',
    'clearcacheonload' => 1,
    'author' => 'Dominique Kreemers',
    'author_email' => 'dominique.kreemers@me.com',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'php' => '7.0.0-7.0.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
