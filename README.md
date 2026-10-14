<div align="center">
    <img src="icon.svg">
    <h1>SuluAITranslatorBundle</h1>
    <blockquote>
        <p dir="auto">Sulu bundle that integrates DeepL API for bulk and single translations of content fields.</p>
    </blockquote>
</div>

![GitHub release](https://flat.badgen.net/github/release/robole-dev/sulu-ai-translator-bundle)
![Supports Sulu 3.0 or later](https://flat.badgen.net/badge/Sulu/3.0/52B5C9?icon=php)

![Demonstration of content field translation](demo.gif)

## Features

- DeepLService to fetch translations and usage statistics from DeepL API
- "Usage statistics" admin view with permission
- Translation button next to input fields
- Toolbar button to bulk translate all fields on page, snippet and article (all article groups) edit views. Every other localized form view with a save action gets the button as well, e.g. the views of your own content bundles (see [Toolbar button configuration](#toolbar-button-configuration))
- Supports Sulu 3

## Installation

This bundle requires PHP 8.2 or later. Make sure to have installed [Node 18](https://nodejs.org/en/) or later for building the Sulu administration UI.

1. Open a command console, enter your project directory and run:

```console
composer require robole/sulu-ai-translator-bundle
```

**⚠️ Sulu 2.6** users please install the v1.1 branch:

```console
composer require robole/sulu-ai-translator-bundle:^1.1
```

If you're **not** using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
return [
    //...
    Robole\SuluAITranslatorBundle\SuluAITranslatorBundle::class => ['all' => true],
];
```

2. Register the new routes by adding the following to your `config/routes/sulu_admin.yaml`:

```yaml
sulu_ai_translator:
    resource: "@SuluAITranslatorBundle/Resources/config/routes_admin.yaml"
    prefix: /admin/api
```

3. Add the file `config/packages/sulu_ai_translator.yaml` with the following configuration:

```yaml
sulu_ai_translator:
    deepl_api_key: "%env(DEEPL_API_KEY)%"
    locale_mapping:
        en: "en-GB"
```

Via `locale_mapping` you can map a locale key from your webspace to the according [official DeepL target language](https://developers.deepl.com/docs/resources/supported-languages#target-languages). Use value `null` for languages that should not be translatable.

4. Reference the frontend code by adding the following to your `assets/admin/package.json`:

```json
"dependencies": {
    "sulu-ai-translator-bundle": "file:../../vendor/robole/sulu-ai-translator-bundle/src/Resources/js"
}
```

5. Import the frontend code by adding the following to your `assets/admin/app.js`:

```javascript
import "sulu-ai-translator-bundle";
```

6. Install all npm dependencies and build the admin UI ([see all options](https://docs.sulu.io/en/2.5/cookbook/build-admin-frontend.html)):

```bash
cd assets/admin
npm install
npm run build
```

7. Add your [Deepl API Key](https://support.deepl.com/hc/en-us/articles/360020695820-API-Key-for-DeepL-s-API#h_01HM9MFQ195GTHM93RRY63M18W) to the `.env` file:

```
DEEPL_API_KEY="..."
```

8. Grant permissions in Sulu backend to access "DeepL Usage Statistics" view.

## Toolbar button configuration

The bulk translate button is added to the toolbar of every view that

- is a form view (`sulu_admin.form` or `sulu_admin.preview_form`),
- is shown per locale (the view itself or its parent tab view has `locales`),
- has a save toolbar action (`sulu_admin.save` or `sulu_admin.save_with_publishing`),
- and does not match one of the `exclude` patterns (by default the settings tabs and the "add" forms).

This covers pages, snippets, articles of all groups and the views of custom content bundles (e.g. built with Sulu's `ContentViewBuilderFactory`) without any code. All settings are optional, these are the defaults:

```yaml
sulu_ai_translator:
    toolbar:
        auto_attach: true       # false: only pages, snippets and articles plus "views"
        views: []               # additional views that always get the button, without any check
        exclude: ['*.settings', '*.add_tabs*', '*.add_form*'] # fnmatch patterns of view names that never get the button automatically
```

Two rules apply on top of that, also to pages, snippets and articles:

- The page tabs "Content", "SEO" and "Excerpt" always get the button. They are listed explicitly because pages do not meet the criteria above (the locales come from the webspace and the save action is part of a dropdown).
- A view whose `resourceKey` differs from the `resourceKey` of its parent view (e.g. the settings tab of a language independent entity inside localized article tabs) does not get the button automatically, because translating it would overwrite the data of all languages. Add it to `views` if you want it anyway.

Example for a view that does not meet the criteria above, and for a view that should not get the button:

```yaml
sulu_ai_translator:
    toolbar:
        views: ['app.custom_form.details']
        exclude: ['*.settings', '*.add_tabs*', '*.add_form*', 'sulu_media.*']
```

Note that setting `exclude` replaces the default, so keep the default patterns in the list if you still want them. To add the button to the "add" forms, remove `'*.add_tabs*'` and `'*.add_form*'`.

## Permissions

By default everyone who can edit a form may translate in it. To restrict this to certain roles, enable the permissions:

```yaml
sulu_ai_translator:
    permissions:
        enabled: false                       # true: only roles with the permission of an area may translate there
        areas: [pages, articles, snippets]   # resource keys with their own permission, all others share the area "other"
```

With `enabled: true` the permission group "AI Translator" in *Settings > User roles* gets one row per area plus the row `other` (column "Edit"). An area is the resource key of the form (`pages`, `articles`, `snippets`, `events`, ...). Pages, snippets and articles of all groups are listed by default; forms of other resources (e.g. your own content bundles, media, categories) fall under `other` unless you add their resource key to `areas`.

Without the permission of its area the user gets neither the toolbar button nor the buttons at the fields, and the translation endpoint answers with 403. The existing permission for the usage statistics is not affected.

Note:

- After enabling the permissions nobody may translate until the roles are updated, so set the roles first or right after the update.
- The resource key is sent by the browser. A user who may translate in any area could therefore have text of another area translated by calling the endpoint directly. This only uses translation quota, nothing is saved (saving is protected by the regular Sulu permissions).

## Limitations

- Currently only supports fields of type `input[type="text"]`, `textarea` and `<CkEditor />`
- Translations are applied on the frontend, giving content creators the ability to check translation quality and undo changes
- Links to internal pages within text fields have to be updated by hand (obviously)

### Local development

1. Add to `repositories` section of `composer.json`:

```json
    "repositories": [
        {
            "type": "path",
            "url": "./../local-path-to-bundle"
        }
    ],
```

2. Install bundle:

> composer require robole/sulu-ai-translator-bundle:@dev

3. Check the coding standards:

> composer php-cs

4. Apply coding standards:

> composer php-cs-fix

### Troubleshooting

If a translation request returns the input text, it is very likely that the language key(s) defined in your `locale_mapping` bundle configuration are not [supported by DeepL](https://developers.deepl.com/docs/resources/supported-languages#target-languages). This will be indicated in the response object.

### Ideas for next versions

- Add Symfony Recipe for quicker installation of bundle.
- Replace `document.querySelector` with store-based approach for toggling blocks.
- Enable configuration of translation strictness for each language (e.g. formal, informal, etc.)
- Add a dropdown popup next to translation button for overwriting source and target language of a field

### Disclaimer

This bundle uses the DeepL API to provide translation services. This project is not affiliated, associated, authorized, endorsed by, or in any way officially connected with [DeepL SE](https://www.deepl.com) or any of its subsidiaries or affiliates. The use of the DeepL API in this bundle is purely for functionality purposes and does not imply any relationship with the DeepL company.

DeepL is a registered trademark of DeepL SE.
