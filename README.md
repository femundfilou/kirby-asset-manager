# Kirby Asset Manager – Add CSS and JS per (block) snippet

This plugin for [Kirby CMS](https://getkirby.com) is designed to streamline your website's performance by loading only what's needed. Keep your page weight in check and ensure fast loading times by registering stylesheets and JavaScript files on a per-snippet or block basis. Only include assets, that are actually used.

## Installation

### Download

Download and copy this repository to `/site/plugins/asset-manager`.

### Git submodule

```
git submodule add https://github.com/femundfilou/kirby-asset-manager.git site/plugins/asset-manager
```

### Composer

```
composer require femundfilou/kirby-asset-manager
```

## Usage
This plugin exposes `$assetManager` to all snippets. With a simple command you can link a stylesheet or javascript file directly within your snippet. It does work for blocks, too.

```php
// site/snippets/blocks/card.php

<?php
  $assetManager->add('css', 'path/to/your/card.css');
  $assetManager->add('js', 'path/to/your/card.js');
?>

<div class="card">...</div>
```

### Output

All added stylesheets will be appended right before `</head>`, all javascript right before `</body>`. Preload links will be appended right after `<head>`. 
If you want more control over where the generated tags will be placed you can use these placeholders in your template which will be replaced with the corresponsing preload, style and script tags.

```html
<head>
	<!-- AssetManager PRELOAD -->
...
	<!-- AssetManager CSS -->
</head>
...
	<!-- AssetManager JS -->
</body>

```



### Using Vite
For those using [kirby-laravel-vite](https://github.com/lukaskleinschmidt/kirby-laravel-vite), you can use it to include single files like this:

```php
// site/snippets/blocks/card.php

<?php
  $assetManager->add('css', vite()->asset('styles/blocks/card.scss'));
?>

<div class="card">...</div>
```

> **Note**  
> Remember to include those assets in your `vite.config.js` as seperate entries.

```js
export default defineConfig({
  laravel([
    'styles/css/app.css',
    'js/app.js',
    'styles/blocks/card.css'
  ]),
});
```

### Options

You can apply the same options available in Kirby's [`css()`](https://getkirby.com/docs/reference/templates/helpers/css) and [`js()`](https://getkirby.com/docs/reference/templates/helpers/js) helper methods as a third argument.

```php
// site/snippets/blocks/card.php

<?php
  $assetManager->add('css', 'path/to/your/card.css', ['media' => 'print']);
  $assetManager->add('js', 'path/to/your/card.js');
?>

<div class="card">...</div>
```

## License

MIT

## Credits

- [Lukas Kleinschmidt](https://github.com/lukaskleinschmidt) (for giving the idea to use a `page.render:after` hook 🙌)
- [Justus Kraft](https://github.com/jukra00)
