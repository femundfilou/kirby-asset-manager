<?php

F::loadClasses([
    'femundfilou\\assetmanager\\assetmanager' => 'lib/AssetManager.php'
], __DIR__);

use Femundfilou\AssetManager\AssetManager;

/* Huge thanks to Lukas Kleinschmidt
 https://gist.github.com/lukaskleinschmidt/a8e9a76e6fce47430277553ca1332705
 */

Kirby::plugin('femundfilou/asset-manager', [
    'hooks' => [
        'page.render:after' => function (string $contentType, string $html) {
            if ($contentType === 'html') {
                // Define the placeholders
                $cssPlaceholder = '<!-- AssetManager CSS -->';
                $jsPlaceholder = '<!-- AssetManager JS -->';

                // Check for the CSS placeholder, if it's not found, use </head>
                if (strpos($html, $cssPlaceholder) === false) {
                    $html = preg_replace_callback('/<\/head>/', function ($matches) {
                        return AssetManager::getInstance()->output('preload') . AssetManager::getInstance()->output('css') . $matches[0];
                    }, $html);
                } else {
                    $html = str_replace($cssPlaceholder, AssetManager::getInstance()->output('preload') . AssetManager::getInstance()->output('css'), $html);
                }

                // Check for the JS placeholder, if it's not found, use </body>
                if (strpos($html, $jsPlaceholder) === false) {
                    $html = preg_replace_callback('/<\/body>/', function ($matches) {
                        return AssetManager::getInstance()->output('js') . $matches[0];
                    }, $html);
                } else {
                    $html = str_replace($jsPlaceholder, AssetManager::getInstance()->output('js'), $html);
                }
            }
            return $html;
        },
    ],
    'components' => [
        'snippet' => function ($kirby, $name, array $data = [], bool $slots = false) {
            $data['assetManager'] = AssetManager::getInstance();

            return $kirby->core()->components()['snippet'](
                $kirby,
                $name,
                $data,
                $slots,
            );
        }
    ],
]);
