<?php

declare(strict_types=1);

namespace Robole\SuluAiTranslatorBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\FormViewBuilderInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

/**
 * - Adds link to settings navigation tab
 * - Connects route with TranslatorConfigView.js 
 */
class AiTranslatorAdmin extends Admin
{
    public const SECURITY_CONTEXT = 'sulu.contact.people'; // @todo Add permissions: 'ai_translator';

    // Key of TranslatorConfigView.js as registered in app.js
    public const TRANSLATION_CONFIG_VIEW = 'ai_translator.config';

    private ViewBuilderFactoryInterface $viewBuilderFactory;
    private SecurityCheckerInterface $securityChecker;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(AiTranslatorAdmin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $AITranslatorAdminNavigationItem = new NavigationItem('app.translator_config_headline');
            $AITranslatorAdminNavigationItem->setPosition(999);
            $AITranslatorAdminNavigationItem->setView(self::TRANSLATION_CONFIG_VIEW);

            $navigationItemCollection->get(Admin::SETTINGS_NAVIGATION_ITEM)->addChild($AITranslatorAdminNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->securityChecker->hasPermission(AiTranslatorAdmin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createViewBuilder(self::TRANSLATION_CONFIG_VIEW, '/translation', self::TRANSLATION_CONFIG_VIEW)
            );
        }

        // Attach translator toolbar to page edit form 
        if ($viewCollection->has('sulu_page.page_edit_form.details')) {
            /** @var FormViewBuilderInterface $pageEditFormViewBuilder */
            $pageEditFormViewBuilder = $viewCollection->get('sulu_page.page_edit_form.details');
            $pageEditFormViewBuilder->addToolbarActions([
                new ToolbarAction('ai_translator.toolbar', ['allow_overwrite' => true]),
            ]);
        }

        // Attach translator toolbar to page form edit form
        if ($viewCollection->has('sulu_form.edit_form.details')) {
            /** @var FormViewBuilderInterface $formEditFormViewBuilder */
            $formEditFormViewBuilder = $viewCollection->get('sulu_form.edit_form.details');
            $formEditFormViewBuilder->addToolbarActions([
                new ToolbarAction('ai_translator.toolbar', ['allow_overwrite' => true]),
            ]);
        }

        // Attach translator toolbar to snippet form edit form
        if ($viewCollection->has('sulu_snippet.edit_form.details')) {
            /** @var FormViewBuilderInterface $snippetEditFormViewBuilder */
            $snippetEditFormViewBuilder = $viewCollection->get('sulu_snippet.edit_form.details');
            $snippetEditFormViewBuilder->addToolbarActions([
                new ToolbarAction('ai_translator.toolbar', ['allow_overwrite' => true]),
            ]);
        }
    }

    public static function getPriority(): int
    {
        return PageAdmin::getPriority() - 1;
    }

    public function getConfigKey(): ?string
    {
        return 'ai_translator';
    }
}
