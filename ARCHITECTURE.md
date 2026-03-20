# Architecture: parser (FuelPHP Parser)

## Purpose

FuelPHP's View Parser package. Extends the core `View` class to support multiple template engines (Twig, Smarty, Mustache, Markdown, Haml, Handlebars, Jade, Dwoo, Lex, PHPTAL) alongside native PHP views.

## Directory Structure

```
classes/
  view.php                  — Base parser view class extending FuelPHP's View
  view/
    twig.php                — Twig adapter
    smarty.php              — Smarty adapter
    mustache.php            — Mustache adapter
    markdown.php            — Markdown adapter (renders .md files to HTML)
    haml.php                — Haml adapter
    hamltwig.php            — Haml+Twig hybrid adapter
    handlebars.php          — Handlebars adapter
    jade.php                — Jade (Pug) adapter
    lex.php                 — Lex (FuelPHP's own template parser) adapter
    dwoo.php                — Dwoo adapter
    phptal.php              — PHPTAL adapter
  smarty/fuel/extension.php — FuelPHP-specific Smarty plugins/helpers
  twig/fuel/extension.php   — FuelPHP-specific Twig functions/filters
```

## Key Design Decisions

- **Adapter pattern**: Each template engine is a separate class that extends `View` and overrides `process_file()`, keeping the View API consistent regardless of engine
- **Auto-detection by extension**: FuelPHP routes `.twig`, `.mustache`, `.md`, etc. files to the appropriate parser class based on file extension
- **Engine isolation**: Each adapter is only loaded when its file type is actually used, so unused engines incur no overhead

## Extension Points

- Add a new template engine by creating a `View_Myengine` class extending `View`, then registering it in the parser config
- Add custom Twig functions in `twig/fuel/extension.php`
