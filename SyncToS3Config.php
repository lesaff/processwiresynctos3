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

class SyncToS3Config extends ModuleConfig
{
    /**
     * Get module settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->add([
            [
                'name' => 'general_fieldset',
                'label' => $this->_('General Settings'),
                'description' => $this->_('Sync To S3 Settings'),
                'icon' => 'cogs',
                'type' => 'fieldset',
                'children' => [
                    [
                        'name' => 's3_bucket_name',
                        'label' => $this->_('S3 Bucket Name'),
                        'type' => 'text',
                        'required' => true,
                        'value' => $this->_(''),
                        'columnWidth' => 100,
                    ],
                    [
                        'name' => 'access_key_id',
                        'label' => $this->_('Access Key ID'),
                        'type' => 'text',
                        'required' => true,
                        'value' => $this->_(''),
                        'columnWidth' => 100,
                    ],
                    [
                        'name' => 'secret_access_key',
                        'label' => $this->_('Secret Access Key'),
                        'type' => 'text',
                        'required' => true,
                        'value' => $this->_(''),
                        'columnWidth' => 100,
                    ],
                    [
                        'name' => 's3_region',
                        'label' => $this->_('Region'),
                        'type' => 'text',
                        'required' => true,
                        'value' => $this->_('us-west-2'),
                        'columnWidth' => 100,
                    ],
                    [
                        'name' => 'cloudfront_url',
                        'label' => $this->_('Cloudfront URL'),
                        'description' => $this->_(''),
                        'type' => 'text',
                        'required' => false,
                        'value' => $this->_(''),
                        'columnWidth' => 100,
                    ],
                    [
                        'name' => 'folders_to_include',
                        'label' => $this->_('List of folders to include'),
                        'description' => $this->_('Path is in relation to your /site folder'),
                        'type' => 'text',
                        'required' => false,
                        'value' => $this->_('/site/assets/files'),
                        'columnWidth' => 100,
                    ],
                ],
            ]
        ]);
    }
}
