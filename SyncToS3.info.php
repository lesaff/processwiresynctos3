<?php

namespace ProcessWire;

/**
 * ProcessWire 'SyncToS3' Module
 *
 * Sync and Backup all page assets uploaded into PW to Amazon S3 and Deliver them via Amazon Cloudfront
 *
 * ProcessWire 3.x or higher
 * Copyright (C) 2025 by Rudy Affandi
 * Licensed under GNU/GPL v2
 *
 * https://github.com/lesaff/processwiresynctos3
 */

$info = [
    // Module meta data
    'title'    => 'Sync to S3',
    'version'  => '1.0.6',
    'author'   => 'Rudy Affandi',
    'summary'  => 'Sync site folder to AWS S3 automatically',
    'autoload' => true,
    'requires' => ['ProcessWire>=3.0'],
    'singular' => true,
    'href'     => 'https://github.com/lesaff/processwiresynctos3',
    'icon'     => 'data',
    'requires' => 'ProcessWire>=3.0',

    // use autoload if module is to be called each load, if it is only needed to setup something set to false
    'autoload' => true,
    'singular' => true,
];
