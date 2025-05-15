<?php

/**
 * Asset Manager Plugin for Kirby CMS
 * @remarks Based on work by Lukas Kleinschmidt
 */

F::loadClasses([
	'femundfilou\\assetmanager\\assetmanager' => 'lib/AssetManager.php'
], __DIR__);

use Femundfilou\AssetManager\AssetManager;

Kirby::plugin('femundfilou/asset-manager', [
	'options' => [
		'preload' => true
	],
	'hooks' => [
		'page.render:after' => function (string $contentType, string $html): string {
			if ($contentType !== 'html') {
				return $html;
			}

			$manager = AssetManager::getInstance();
			$cssContent = $manager->output('css');
			$jsContent = $manager->output('js');

			// Insert Preload
			if (true === option('femundfilou.asset-manager.preload')) {
				$preloadContent = $manager->output('preload');
				$html = str_contains($html, '<!-- AssetManager PRELOAD -->')
					? str_replace('<!-- AssetManager PRELOAD -->', $preloadContent, $html)
					: preg_replace('/(<head\b[^>]*>)/i', '$1' . PHP_EOL . $preloadContent, $html);
			}

			// Insert CSS
			$html = str_contains($html, '<!-- AssetManager CSS -->')
				? str_replace('<!-- AssetManager CSS -->', $cssContent, $html)
				: preg_replace('/(<head\b[^>]*>)/i', '$1' . PHP_EOL . $cssContent, $html);

			// Insert JS
			return str_contains($html, '<!-- AssetManager JS -->')
				? str_replace('<!-- AssetManager JS -->', $jsContent, $html)
				: preg_replace('/<\/body>/i', $jsContent . '$0', $html);
		}
	],
	'components' => [
		'snippet' => function ($kirby, $name, array $data = [], bool $slots = false) {
			$data['assetManager'] = AssetManager::getInstance();
			return $kirby->core()->components()['snippet']($kirby, $name, $data, $slots);
		}
	]
]);
