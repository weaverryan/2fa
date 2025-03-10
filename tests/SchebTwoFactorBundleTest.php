<?php

declare(strict_types=1);

namespace Scheb\TwoFactorBundle\Tests;

use Scheb\TwoFactorBundle\DependencyInjection\Compiler\MailerCompilerPass;
use Scheb\TwoFactorBundle\DependencyInjection\Compiler\TwoFactorFirewallConfigCompilerPass;
use Scheb\TwoFactorBundle\DependencyInjection\Compiler\TwoFactorProviderCompilerPass;
use Scheb\TwoFactorBundle\DependencyInjection\Factory\Security\TwoFactorFactory;
use Scheb\TwoFactorBundle\SchebTwoFactorBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function count;

class SchebTwoFactorBundleTest extends TestCase
{
    /**
     * @test
     */
    public function build_initializeBundle_addCompilerPass(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);

        $compilerPasses = [
            $this->isInstanceOf(TwoFactorProviderCompilerPass::class),
            $this->isInstanceOf(TwoFactorFirewallConfigCompilerPass::class),
            $this->isInstanceOf(MailerCompilerPass::class),
        ];

        //Expect compiler pass to be added
        $containerBuilder
            ->expects($this->exactly(count($compilerPasses)))
            ->method('addCompilerPass')
            ->with($this->logicalOr(...$compilerPasses));

        //Expect register authentication provider factory
        $securityExtension = $this->createMock(SecurityExtension::class);
        $containerBuilder
            ->expects($this->once())
            ->method('getExtension')
            ->with('security')
            ->willReturn($securityExtension);
        $securityExtension
            ->expects($this->once())
            ->method('addAuthenticatorFactory')
            ->with($this->isInstanceOf(TwoFactorFactory::class));

        $bundle = new SchebTwoFactorBundle();
        $bundle->build($containerBuilder);
    }
}
