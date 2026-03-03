<?php

declare(strict_types=1);

namespace Robole\SuluAITranslatorBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

/**
 * - Adds link to settings navigation tab
 * - Connects route with TranslatorConfigView.js.
 */
class AITranslatorAdmin extends Admin
{
    public const SECURITY_CONTEXT = 'sulu.settings.access_usage_statistics';

    public const TRANSLATION_CONFIG_VIEW = 'ai_translator.config'; // Key of TranslatorConfigView.js as registered in app.js

    public const SULU_ARTICLE_EDIT_VIEW = 'sulu_article.article.edit_tabs_default.content'; // @todo target group: https://github.com/sulu/sulu/blob/3.0/packages/article/src/Infrastructure/Sulu/Admin/ArticleAdmin.php#L128
    public const SULU_PAGE_EDIT_VIEW = 'sulu_page.page_edit_form.content';
    public const SULU_SNIPPET_EDIT_VIEW = 'sulu_snippet.snippet.edit_tabs.content';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $this->addUsageStatisticsNavigationItem($navigationItemCollection);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $this->addUsageStatisticsSettingsView($viewCollection);
        $this->addPageEditToolbarAction($viewCollection);
        $this->addArticleEditToolbarAction($viewCollection);
        $this->addSnippetEditToolbarAction($viewCollection);
    }

    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'AI Translator' => [
                    self::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                    ],
                ],
            ],
        ];
    }

    private function addUsageStatisticsNavigationItem(NavigationItemCollection $navigationItemCollection): void
    {
        if (!$this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            return;
        }

        $AITranslatorAdminNavigationItem = new NavigationItem('app.translator_config_headline');
        $AITranslatorAdminNavigationItem->setPosition(999);
        $AITranslatorAdminNavigationItem->setView(self::TRANSLATION_CONFIG_VIEW);

        $navigationItemCollection->get(Admin::SETTINGS_NAVIGATION_ITEM)->addChild($AITranslatorAdminNavigationItem);
    }

    private function addUsageStatisticsSettingsView(ViewCollection $viewCollection): void
    {
        if (!$this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            return;
        }

        $viewCollection->add(
            $this->viewBuilderFactory->createViewBuilder(
                self::TRANSLATION_CONFIG_VIEW,
                '/translator',
                self::TRANSLATION_CONFIG_VIEW
            )
        );
    }

    private function addPageEditToolbarAction(ViewCollection $viewCollection): void
    {
        try {
            $pageView = $viewCollection->get(self::SULU_PAGE_EDIT_VIEW);
            $pageView->setOption('toolbarActions', [
                ...$pageView->getView()->getOption('toolbarActions'),
                new ToolbarAction('ai_translator.toolbar')
            ]);
            $viewCollection->add($pageView);
        } catch (\Exception) {
            // View not available
        }
    }

    private function addArticleEditToolbarAction(ViewCollection $viewCollection): void
    {
        try {
            $articleEditView = $viewCollection->get(self::SULU_ARTICLE_EDIT_VIEW);
            $articleEditView->setOption('toolbarActions', [
                ...$articleEditView->getView()->getOption('toolbarActions'),
                new ToolbarAction('ai_translator.toolbar')
            ]);
            $viewCollection->add($articleEditView);
        } catch (\Exception) {
            // View not available
        }
    }

    private function addSnippetEditToolbarAction(ViewCollection $viewCollection): void
    {
        try {
            $snippetEditView = $viewCollection->get(self::SULU_SNIPPET_EDIT_VIEW);
            $snippetEditView->setOption('toolbarActions', [
                ...$snippetEditView->getView()->getOption('toolbarActions'),
                new ToolbarAction('ai_translator.toolbar')
            ]);
            $viewCollection->add($snippetEditView);
        } catch (\Exception) {
            // View not available
        }
    }
}
