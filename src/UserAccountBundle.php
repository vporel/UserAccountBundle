<?php
namespace UserAccountBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
class UserAccountBundle extends AbstractBundle{

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
        ->children()
            ->scalarNode("repository_class")->isRequired()->end() //User repository class
            ->booleanNode("absolute_email_validation")->defaultValue(false)->end()
            ->scalarNode("signup_url")->defaultValue("/creer-un-compte")->end()
            ->scalarNode("email_validation_template")->defaultValue("@UserAccount/emails/email-validation.html.twig")->end()
            ->scalarNode("password_reset_template")->defaultValue("@UserAccount/emails/password-reset.html.twig")->end()
        ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(dirname(__DIR__)."/config/services.yaml");
        $container->parameters()->set("user_account", $config);
        $builder->addAliases(["UserAccountBundle\Repository\UserRepositoryInterface" => $config["repository_class"]]);
    }
    
}