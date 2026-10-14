<?php

declare(strict_types=1);

namespace Robole\SuluAITranslatorBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\FormViewBuilder;
use Sulu\Bundle\AdminBundle\Admin\View\PreviewFormViewBuilder;
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

    public const SULU_ARTICLE_EDIT_VIEW = 'sulu_article.article.edit_tabs_default.content';
    public const SULU_ARTICLE_EDIT_VIEW_PATTERN = '/^sulu_article\.article\.edit_tabs_.+\.content$/'; // one view per article group
    public const SULU_PAGE_EDIT_VIEW = 'sulu_page.page_edit_form.content';
    public const SULU_PAGE_EDIT_VIEW_PATTERN = '/^sulu_page\.page_edit_form\.(content|seo|excerpt)$/';
    public const SULU_SNIPPET_EDIT_VIEW = 'sulu_snippet.snippet.edit_tabs.content';

    public const TOOLBAR_ACTION = 'ai_translator.toolbar'; // Key of AITranslatorToolbarAction.js as registered in app.js

    /**
     * Toolbar actions that save the form. Content entities (pages, articles, snippets, ...) use
     * "save_with_publishing", other resources use "save".
     */
    private const SAVE_TOOLBAR_ACTIONS = ['sulu_admin.save', 'sulu_admin.save_with_publishing'];

    /**
     * @param string[] $attachViews     names of views that always get the toolbar action, without any check
     * @param string[] $excludePatterns fnmatch patterns of view names that never get the toolbar action automatically
     */
    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
        private readonly bool $autoAttach = true,
        private readonly array $attachViews = [],
        private readonly array $excludePatterns = ['*.settings', '*.add_tabs*', '*.add_form*']
    ) {
    }

    /**
     * Sulu sorts the admins by priority (tagged_iterator('sulu.admin', defaultPriorityMethod: 'getPriority')).
     * With the default priority 0 it depends on the load order whether the views of other bundles already
     * exist in configureViews(). A low priority makes this admin run after all others.
     */
    public static function getPriority(): int
    {
        return -1024;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $this->addUsageStatisticsNavigationItem($navigationItemCollection);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $this->addUsageStatisticsSettingsView($viewCollection);
        $this->addToolbarActions($viewCollection);
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

    private function addToolbarActions(ViewCollection $viewCollection): void
    {
        foreach (array_keys($viewCollection->all()) as $viewName) {
            $viewName = (string) $viewName;

            if ($this->isExcluded($viewName)) {
                continue;
            }

            if ($this->hasForeignResourceKey($viewCollection, $viewName)) {
                continue;
            }

            if ($this->isKnownEditView($viewName) || ($this->autoAttach && $this->isLocalizedSaveForm($viewCollection, $viewName))) {
                $this->addToolbarAction($viewCollection, $viewName);
            }
        }

        foreach ($this->attachViews as $viewName) {
            if ($viewCollection->has($viewName)) {
                $this->addToolbarAction($viewCollection, $viewName);
            }
        }
    }

    /**
     * Pages are not found by the automatic detection: their locales come from the webspace (no "locales" option)
     * and the save action is part of a dropdown. Therefore all page tabs that contain fields are listed here.
     */
    private function isKnownEditView(string $viewName): bool
    {
        return self::SULU_PAGE_EDIT_VIEW === $viewName
            || 1 === preg_match(self::SULU_PAGE_EDIT_VIEW_PATTERN, $viewName)
            || self::SULU_SNIPPET_EDIT_VIEW === $viewName
            || self::SULU_ARTICLE_EDIT_VIEW === $viewName
            || 1 === preg_match(self::SULU_ARTICLE_EDIT_VIEW_PATTERN, $viewName);
    }

    /**
     * A view whose resource differs from the resource of its parent view is a tab of another entity inside the
     * tabs of the parent, e.g. the settings of a language independent entity inside localized tabs. It would
     * inherit the locales of the parent, but translating it would overwrite the data of all languages.
     */
    private function hasForeignResourceKey(ViewCollection $viewCollection, string $viewName): bool
    {
        try {
            $view = $viewCollection->get($viewName)->getView();
            $parentName = $view->getParent();

            if (null === $parentName || !$viewCollection->has($parentName)) {
                return false;
            }

            $resourceKey = $view->getOption('resourceKey');
            $parentResourceKey = $viewCollection->get($parentName)->getView()->getOption('resourceKey');

            return null !== $resourceKey && null !== $parentResourceKey && $resourceKey !== $parentResourceKey;
        } catch (\Exception) {
            return false;
        }
    }

    private function isExcluded(string $viewName): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $viewName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A form view that is shown per locale (itself or via its parent tab view) and has a save action.
     */
    private function isLocalizedSaveForm(ViewCollection $viewCollection, string $viewName): bool
    {
        try {
            $view = $viewCollection->get($viewName)->getView();

            if (!in_array($view->getType(), [FormViewBuilder::TYPE, PreviewFormViewBuilder::TYPE], true)) {
                return false;
            }

            if (!$this->hasLocales($viewCollection, $viewName)) {
                return false;
            }

            foreach ($view->getOption('toolbarActions') ?? [] as $toolbarAction) {
                if ($toolbarAction instanceof ToolbarAction && in_array($toolbarAction->getType(), self::SAVE_TOOLBAR_ACTIONS, true)) {
                    return true;
                }
            }
        } catch (\Exception) {
            // View not available or incomplete
        }

        return false;
    }

    private function hasLocales(ViewCollection $viewCollection, string $viewName): bool
    {
        $visited = [];

        while (null !== $viewName && !isset($visited[$viewName]) && $viewCollection->has($viewName)) {
            $visited[$viewName] = true;
            $view = $viewCollection->get($viewName)->getView();

            if (!empty($view->getOption('locales'))) {
                return true;
            }

            $viewName = $view->getParent();
        }

        return false;
    }

    private function addToolbarAction(ViewCollection $viewCollection, string $viewName): void
    {
        try {
            $viewBuilder = $viewCollection->get($viewName);
            $toolbarActions = $viewBuilder->getView()->getOption('toolbarActions') ?? [];

            foreach ($toolbarActions as $toolbarAction) {
                if ($toolbarAction instanceof ToolbarAction && self::TOOLBAR_ACTION === $toolbarAction->getType()) {
                    return;
                }
            }

            $viewBuilder->setOption('toolbarActions', [
                ...array_values($toolbarActions),
                new ToolbarAction(self::TOOLBAR_ACTION),
            ]);
            $viewCollection->add($viewBuilder);
        } catch (\Exception) {
            // View not available
        }
    }
}
