## Sulu AI Translator Bundle

Integrates DeepL API for bulk and single translation of content fields.

- @todo Add docs on how to integrate with a custom React input component
- @todo Update README providing installation steps.

@todo:
This **prototype** enables a one-click translation of content fields into the selected page language. It will be rendered on the detail view of pages, snippets and forms where it attaches an icon to the toolbar for bulk translation and a button next to each input (input[type="text"], textarea, CkEditor).

This Sulu component is entirely written in vanilla JavaScript. To keep things simple it does not use React, nor Sulu's own router, nor does it integrate with Sulu's PHP API to attach custom navigation links.

### Further ideas

A non-prototypal solution would require an integration with Sulu UI API and probably the overriding of core UI components (e.g. `<Input />). Besides that, one could imagine the following features:

-   Configuration of translation strictness (e.g. formal, informal, etc.) and overview of the number of translation strings used in the backend
-   Dropdown on translation button (see wireframe) to precisely define source and target language
