<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2018 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Tests\Controller\ContentElement;

use Contao\CoreBundle\Tests\Fixtures\Controller\FrontendModule\TestController;
use Contao\CoreBundle\Tests\TestCase;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class FrontendModuleControllerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $controller = new TestController();

        $this->assertInstanceOf('Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController', $controller);
        $this->assertInstanceOf('Contao\CoreBundle\Controller\AbstractFragmentController', $controller);
    }

    public function testCreatesTemplateFromClassname(): void
    {
        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_test'));

        $controller(new Request([], [], ['_scope' => 'frontend']), new ModuleModel(), 'main');
    }

    public function testCreatesTemplateFromFragmentOptionsType(): void
    {
        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_foo'));
        $controller->setFragmentOptions(['type' => 'foo']);

        $controller(new Request(), new ModuleModel(), 'main');
    }

    public function testCreatesTemplateFromCustomTpl(): void
    {
        $model = new ModuleModel();
        $model->customTpl = 'mod_bar';

        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_bar'));

        $controller(new Request(), $model, 'main');
    }

    public function testSetsClassFromType(): void
    {
        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_test'));

        $response = $controller(new Request(), new ModuleModel(), 'main');
        $template = json_decode($response->getContent());

        $this->assertSame('', $template->cssID);
        $this->assertSame('mod_test', $template->class);
    }

    public function testSetsHeadlineFromModel(): void
    {
        $model = new ModuleModel();
        $model->headline = serialize(['unit' => 'h6', 'value' => 'foobar']);

        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_test'));

        $response = $controller(new Request(), $model, 'main');
        $template = json_decode($response->getContent());

        $this->assertSame('foobar', $template->headline);
        $this->assertSame('h6', $template->hl);
    }

    public function testSetsCssIDAndClassFromModel(): void
    {
        $model = new ModuleModel();
        $model->cssID = serialize(['foo', 'bar']);

        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_test'));

        $response = $controller(new Request(), $model, 'main');
        $template = json_decode($response->getContent());

        $this->assertSame(' id="foo"', $template->cssID);
        $this->assertSame('mod_test bar', $template->class);
    }

    public function testSetsSection(): void
    {
        $controller = new TestController();
        $controller->setContainer($this->mockContainerWithFrameworkTemplate('mod_test'));

        $response = $controller(new Request(), new ModuleModel(), 'left');
        $template = json_decode($response->getContent());

        $this->assertSame('left', $template->inColumn);
    }

    private function mockContainerWithFrameworkTemplate(string $templateName, $scope = 'backend')
    {
        $framework = $this->mockContaoFramework();
        $framework
            ->expects($this->once())
            ->method('createInstance')
            ->with(FrontendTemplate::class, [$templateName])
            ->willReturn(new FrontendTemplate())
        ;

        $container = new ContainerBuilder();
        $container->set('contao.framework', $framework);
        $container->set('contao.routing.scope_matcher', $this->mockScopeMatcher());

        return $container;
    }
}
