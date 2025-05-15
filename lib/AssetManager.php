<?php

namespace Femundfilou\AssetManager;

/**
 * Manages CSS and JS assets for web applications
 * @remarks Implements singleton pattern for centralized asset management
 */
class AssetManager
{
	private const VALID_TYPES = ['css', 'js', 'preload'];
	private static ?self $instance = null;

	private array $preload = [];
	private array $css = [];
	private array $js = [];

	private function __construct() {}
	private function __clone() {}

	public function __wakeup(): void
	{
		throw new \Exception('Cannot unserialize singleton');
	}

	public static function getInstance(): self
	{
		return self::$instance ??= new self();
	}

	/**
	 * Adds asset file to manager
	 * @throws \InvalidArgumentException for invalid asset types
	 */
	public function add(string $type, string $filePath, array|string|null $options = null): void
	{
		if (!in_array($type, ['css', 'js'], true)) {
			throw new \InvalidArgumentException("Invalid type: {$type}");
		}

		$uniqueId = md5($type . $filePath . json_encode($options));

		// Add the asset to its respective collection
		$this->{$type}[] = $type === 'css'
			? css($filePath, $options)
			: js($filePath, $options);

		// Only generate preload tags for JS files
		if ($type === 'js') {
			$this->preload[$uniqueId] = $this->generatePreloadTag($filePath, $options);
		}
	}

	/**
	 * Generates preload tag for JS assets
	 * 
	 */
	private function generatePreloadTag(string $filePath, array|string|null $options): string
	{
		$attributes = [
			'rel' => 'modulepreload',
			'href' => $filePath,
			'crossorigin' => $options['crossorigin'] ?? false,
		];

		return \Kirby\Toolkit\Html::tag('link', '', $attributes);
	}

	/**
	 * Outputs registered assets as string
	 * @throws \InvalidArgumentException for invalid asset types
	 */
	public function output(string $type): string
	{
		if (!in_array($type, self::VALID_TYPES, true)) {
			throw new \InvalidArgumentException("Invalid type: {$type}");
		}

		if ($type === 'preload') {
			return implode(PHP_EOL, array_values($this->preload));
		}

		return implode(PHP_EOL, $this->{$type});
	}

	public static function resetInstance(): void
	{
		self::$instance = null;
	}
}
