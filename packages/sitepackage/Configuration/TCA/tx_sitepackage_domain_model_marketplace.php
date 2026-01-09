<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'Marketplace',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:sitepackage/Resources/Public/Icons/tx_sitepackage_domain_model_marketplace.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                title, description, images, files,
                --div--;Access, hidden, starttime, endtime
            ',
        ],
    ],
    'columns' => [
        'hidden' => [
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle'],
        ],
        'starttime' => [
            'config' => ['type' => 'datetime'],
        ],
        'endtime' => [
            'config' => ['type' => 'datetime'],
        ],
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'max' => 255,
            ],
        ],
        'description' => [
            'label' => 'Description',
            'config' => [
                'type' => 'text',
            ],
        ],
        'images' => [
            'label' => 'Images',
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Add images',
                ],
                'allowed' => 'jpg,jpeg,png,bmp',
                'maxitems' => 20,
                'minitems' => 0,
            ],
        ],
        'files' => [
            'label' => 'Files',
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Add files',
                ],
                'allowed' => 'pdf,doc,docx,xls,xlsx,txt',
                'maxitems' => 20,
                'minitems' => 0,
            ],
        ],
    ],
];
