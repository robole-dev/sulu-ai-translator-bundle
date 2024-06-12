# AITranslatorBundle

**Sulu bundle that integrates DeepL API for bulk and single translations of content fields.**

AITranslatorBundle features:

-   DeepLService to fetch translations and usage statistics from DeepL API
-   "Usage statistics" admin view with permission
-   Translation button next to input fields
-   Toolbar button to bulk translate all fields (currently only for pages, snippets and forms)

More features coming soon (see below)!

## Installation

This bundle requires PHP 8.2. Make sure to have installed [Node 18](https://nodejs.org/en/) (or Node 14 for Sulu versions <2.6.0) for building the Sulu administration UI.

1. Open a command console, enter your project directory and run:

```console
composer require robole/sulu-ai-translator-bundle
```

If you're **not** using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
return [
    //...
    Robole\SuluAITranslatorBundle\SuluAITranslatorBundle::class => ['all' => true],
];
```

2. Register the new routes by adding the following to your `routes_admin.yaml`:

```yaml
SuluAITranslatorBundle:
    resource: "@SuluAITranslatorBundle/Resources/config/routes_admin.yml"
```

3. Reference the frontend code by adding the following to your `assets/admin/package.json`:

```json
"dependencies": {
    "sulu-ai-translator-bundle": "file:../../vendor/robole/sulu-ai-translator-bundle/src/Resources/js"
}
```

4. Import the frontend code by adding the following to your `assets/admin/app.js`:

```javascript
import "sulu-ai-translator-bundle";
```

5. Install all npm dependencies and build the admin UI ([see all options](https://docs.sulu.io/en/2.5/cookbook/build-admin-frontend.html)):

```bash
cd assets/admin
npm install
npm run build
```

6. Add your [Deepl API Key](https://support.deepl.com/hc/en-us/articles/360020695820-API-Key-for-DeepL-s-API#h_01HM9MFQ195GTHM93RRY63M18W) to the `.env` file:

```
DEEPL_API_KEY="..."
```

7. Grant permissions in Sulu backend to access "DeepL Usage Statistics" view.

## Limitations

-   Currently only supports fields of type `input[type="text"]`, `textarea` and `<CkEditor />`
-   Translations are applied on the frontend, giving content creators the ability to check translation quality and undo changes
-   Links to internal pages have to be updated by hand

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

### Troubleshooting

If a translation request fails, make sure that the `source` and `target` language keys are [supported by DeepL](https://developers.deepl.com/docs/resources/supported-languages#target-languages). If not, extend `TranslationController->getLanguageKey()`.

### Ideas for next major version

-   Provide configuration parameter for mapping custom locale codes to DeepL target language code.
-   Add Symfony Recipe for quicker installation of bundle.
-   Replace `document.querySelector` with store-based approach for toggling blocks.
-   Find a less verbose way to wrap sulu core input components with AI-translate-button.
-   Enable configuration of translation strictness for each language (e.g. formal, informal, etc.)
-   Add a dropdown popup next to translation button for overwriting source and target language of a field
