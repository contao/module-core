<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Installs the required Contao directories.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class InstallCommand extends AbstractLockedCommand
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @var array
     */
    private $emptyDirs = [
        'system',
        'system/config',
        'templates',
        '%s/system',
    ];

    /**
     * @var array
     */
    private $ignoredDirs = [
        'assets/css',
        'assets/js',
        'system/cache',
        'system/modules',
        'system/themes',
        'system/tmp',
        '%s/share',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('contao:install')
            ->setDefinition([
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
            ])
            ->setDescription('Installs the required Contao directories')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->fs = new Filesystem();
        $this->io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->getParameter('kernel.project_dir');
        $this->webDir = rtrim($input->getArgument('target'), '/');

        $this->addEmptyDirs();
        $this->setupUploadDir();
        $this->addIgnoredDirs();

        if (!empty($this->rows)) {
            $this->io->newLine();
            $this->io->listing($this->rows);
        }

        $this->addInitializePhp();

        return 0;
    }

    /**
     * Adds the empty directories.
     */
    private function addEmptyDirs()
    {
        foreach ($this->emptyDirs as $path) {
            $this->addEmptyDir($this->rootDir.'/'.sprintf($path, $this->webDir));
        }
    }

    /**
     * Sets up the upload directory (upload_path - "files" by default)
     */
    private function setupUploadDir()
    {
        $uploadPath = $this->rootDir.'/'.$this->getContainer()->getParameter('contao.upload_path');

        $this->addEmptyDir($uploadPath);

        // Add best practice folder "public" if not empty
        $files = Finder::create()
                    ->ignoreDotFiles(true)
                    ->in($uploadPath)
                    ->count();

        if (0 === $files) {
            $this->addEmptyDir($uploadPath . '/public');
            $this->fs->dumpFile($uploadPath . '/public/.public', '');
        }
    }

    /**
     * Adds an empty directory.
     *
     * @param string $path
     */
    private function addEmptyDir($path)
    {
        if ($this->fs->exists($path)) {
            return;
        }

        $this->fs->mkdir($path);

        $this->rows[] = str_replace($this->rootDir.'/', '', $path);
    }

    /**
     * Adds the ignored directories.
     */
    private function addIgnoredDirs()
    {
        foreach ($this->ignoredDirs as $path) {
            $this->addIgnoredDir($this->rootDir.'/'.sprintf($path, $this->webDir));
        }

        $this->addIgnoredDir($this->getContainer()->getParameter('contao.image.target_dir'));
    }

    /**
     * Adds a directory with a .gitignore file.
     *
     * @param string $path
     */
    private function addIgnoredDir($path)
    {
        $this->addEmptyDir($path);

        if ($this->fs->exists($path.'/.gitignore')) {
            return;
        }

        $this->fs->dumpFile(
            $path.'/.gitignore',
            "# Create the folder and ignore its content\n*\n!.gitignore\n"
        );
    }

    /**
     * Adds the initialize.php file.
     */
    private function addInitializePhp()
    {
        $this->fs->dumpFile(
            $this->rootDir.'/system/initialize.php',
            <<<'EOF'
<?php

use Contao\CoreBundle\Response\InitializeControllerResponse;
use Symfony\Component\HttpFoundation\Request;

if (!defined('TL_SCRIPT')) {
    die('Your script is not compatible with Contao 4.');
}

/** @var Composer\Autoload\ClassLoader */
$loader = require __DIR__ . '/../app/autoload.php';

$request = Request::create('/_contao/initialize', 'GET', [], $_COOKIE, [], $_SERVER);
$request->attributes->set('_scope', ('BE' === TL_MODE ? 'backend' : 'frontend'));

$kernel = new AppKernel('prod', false);
$response = $kernel->handle($request);

// Send the response if not generated by the InitializeController
if (!($response instanceof InitializeControllerResponse)) {
    $response->send();
    $kernel->terminate($request, $response);
    exit;
}

EOF
        );

        $this->io->text("Added/updated the <comment>system/initialize.php</comment> file.\n");
    }
}
