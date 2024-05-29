# AITranslatorBundle

**Sulu bundle that integrates DeepL API for bulk and single translations of content fields (pages, snippets, forms).** This bundle is in alpha-stage and should be treated as such.

AITranslatorBundle features:

- DeeplService to fetch translations and usage statistics from DeepL API
- Usage statistics admin view
- Translation button next to input fields
- Toolbar button to bulk translate all fields
- Permissions for using and administrating the bundle

## Installation

Make sure to have installed [Node 18](https://nodejs.org/en/) (or Node 14 for Sulu versions <2.6.0) for building the Sulu administration UI.

1. Open a command console, enter your project directory and execute:

```console
composer require robole/sulu-ai-translator-bundle
```

If you're **not** using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
return [
    //...
    Robole\SuluAiTranslatorBundle\SuluAiTranslatorBundle::class => ['all' => true],
];
```

2. Register the new routes by adding the following to your `routes_admin.yaml`:

```yaml
SuluAiTranslatorBundle:
  resource: "@SuluAiTranslatorBundle/Resources/config/routes_admin.yml"
```

3. Reference the frontend code by adding the following to your `assets/admin/package.json`:

```json
"dependencies": {
    // ...
    "sulu-ai-translator-bundle": "file:../../vendor/robole/sulu-ai-translator-bundle/src/Resources/js"
}
```

4. Import the frontend code by adding the following to your `assets/admin/app.js`:

```javascript
import "sulu-ai-translator-bundle";
```

5. Install all npm dependencies and build the admin UI ([see all options](https://docs.sulu.io/en/2.5/cookbook/build-admin-frontend.html)):

```bash
cd /assets/admin
npm install
npm run build
```

6. Add your [Deepl API Key](https://support.deepl.com/hc/en-us/articles/360020695820-API-Key-for-DeepL-s-API#h_01HM9MFQ195GTHM93RRY63M18W) to the `.env` file:

```
DEEPL_API_KEY="..."
```

(7. @todo Grant permissions in Sulu backend)

## Details

- Currently supports fields of type `input[type="text"]`, `textarea` and `<CkEditor />`
- Translations are applied on the frontend, giving content creators the ability to check translation quality first and undo changes
- Most of the frontend code uses React and is Sulu-compatible. Some actions, however, rely on `document.querySelector`

### Local development

1. Add to `repositories` section of `composer.json`:

```json
    "repositories": [
        {
            "type": "path",
            "url": "./../../../SuluAITranslatorBundle"
        }
    ],
```

2. Install bundle:
   > composer require robole/sulu-ai-translator-bundle:@dev

### Troubleshooting

If a translation request fails, make sure that the `source` and `target` language keys are [supported by DeepL](https://developers.deepl.com/docs/resources/supported-languages#target-languages). If not, extend `TranslationController->getLanguageKey()`.

### Ideas for a RC

- LICENSE
- Find a less verbose way to wrap sulu core input components and toggle blocks.
- Enable configuration of translation strictness (e.g. formal, informal, etc.)
- Add a dropdown on translation button (see wireframe) to precisely define source and target language
