# AITranslatorBundle

**Sulu bundle that integrates DeepL API for bulk and single translations of content fields.**

![Demonstration of content field translation](https://github.com/robole-dev/sulu-ai-translator-bundle/blob/main/demo.gif)

AITranslatorBundle features:

-   DeepLService to fetch translations and usage statistics from DeepL API
-   "Usage statistics" admin view with permission
-   Translation button next to input fields
-   Toolbar button to bulk translate all fields (currently only for pages, snippets and forms)

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

## Limitations

-   Currently only supports fields of type `input[type="text"]`, `textarea` and `<CkEditor />`
-   Translations are applied on the frontend, giving content creators the ability to check translation quality and undo changes
-   Links to internal pages within text fields have to be updated by hand (obviously)

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

If a translation request returns the input text, it is very likely that the language key(s) defined in your `locale_mapping` bundle configuration are not [supported by DeepL](https://developers.deepl.com/docs/resources/supported-languages#target-languages). This will be indicated in the response object.

### Ideas for next major version

-   Add Symfony Recipe for quicker installation of bundle.
-   Replace `document.querySelector` with store-based approach for toggling blocks.
-   Enable configuration of translation strictness for each language (e.g. formal, informal, etc.)
-   Add a dropdown popup next to translation button for overwriting source and target language of a field
