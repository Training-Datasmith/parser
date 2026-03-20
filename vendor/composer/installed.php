<?php return array(
    'root' => array(
        'name' => 'fuel/parser',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => '0375ce7d7871c222038e56d0541773152cf0e68f',
        'type' => 'fuel-package',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'fuel/parser' => array(
            'pretty_version' => 'dev-develop',
            'version' => 'dev-develop',
            'reference' => '0375ce7d7871c222038e56d0541773152cf0e68f',
            'type' => 'fuel-package',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
