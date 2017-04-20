<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Test\Config\Loader;

use Contao\CoreBundle\Config\Loader\PhpFileLoader;
use Contao\CoreBundle\Test\TestCase;

/**
 * Tests the PhpFileLoader class.
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 */
class PhpFileLoaderTest extends TestCase
{
    /**
     * @var PhpFileLoader
     */
    private $loader;

    /**
     * Creates the PhpFileLoader object.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loader = new PhpFileLoader();
    }

    /**
     * Tests the object instantiation.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('Contao\CoreBundle\Config\Loader\PhpFileLoader', $this->loader);
    }

    /**
     * Tests the supports() method.
     */
    public function testSupports()
    {
        $this->assertTrue(
            $this->loader->supports(
                $this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/config/config.php'
            )
        );

        $this->assertFalse(
            $this->loader->supports(
                $this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/languages/en/default.xlf'
            )
        );
    }

    /**
     * Tests the load() method.
     */
    public function testLoad()
    {
        $expects = <<<'EOF'

$GLOBALS['TL_TEST'] = true;

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load($this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/config/config.php')
        );

        $expects = <<<'EOF'

$GLOBALS['TL_DCA']['tl_test'] = [
    'config' => [
        'dataContainer' => 'DC_Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
    ],
];

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load($this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/dca/tl_test.php')
        );
    }

    /**
     * Test loading a file with a custom namespace.
     */
    public function testLoadNamespace()
    {
        $expects = <<<'EOF'

namespace Foo\Bar {
//namespace Foo\Bar;

$GLOBALS['TL_DCA']['tl_test']['config']['dataContainer'] = 'DC_Table';
}

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load(
                $this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/dca/tl_namespace.php',
                PhpFileLoader::NAMESPACED
            )
        );

        $expects = <<<'EOF'

namespace  {
$GLOBALS['TL_TEST'] = true;
}

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load(
                $this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/languages/en/tl_test.php',
                PhpFileLoader::NAMESPACED
            )
        );
    }

    /**
     * Test loading a file with a declare statement.
     */
    public function testLoadDeclare()
    {
        $expects = <<<'EOF'

/**
 * I am a declare(strict_types=1) comment
 */

//declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_test']['config']['dataContainer'] = 'DC_Table';

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load($this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/dca/tl_declare.php')
        );

        $expects = <<<'EOF'

//declare(strict_types  =   1,ticks = 1);

$GLOBALS['TL_DCA']['tl_test']['config']['dataContainer'] = 'DC_Table';

EOF;

        $this->assertEquals(
            $expects,
            $this->loader->load($this->getRootDir().'/vendor/contao/test-bundle/Resources/contao/dca/tl_declare2.php')
        );
    }
}
