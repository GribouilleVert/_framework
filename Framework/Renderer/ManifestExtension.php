<?php
namespace Framework\Renderer;

use App\Renderer\Exceptions\AssetResolutionException;
use App\Renderer\Exceptions\NoManifestException;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;
use stdClass;

class ManifestExtension implements ExtensionInterface {

    private const DS = DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    private string $simpleAssetsPath;

    /**
     * @var string
     */
    private string $simpleAssetsPublicPath;

    /**
     * @var string
     */
    private string $packedAssetsPath;

    /**
     * @var string
     */
    private string $packedAssetsPublicPath;

    /**
     * @var stdClass|null
     */
    private ?stdClass $manifest;

    public function __construct(ContainerInterface $container)
    {
        $this->simpleAssetsPath = $container->get('assets.defaultPath');
        $this->simpleAssetsPublicPath = $container->get('assets.defaultPublicPath');
        $this->packedAssetsPath = $container->get('assets.bundledPath');
        $this->packedAssetsPublicPath = $container->get('assets.bundledPublicPath');

        $manifest = $container->get('assets.manifest');
        if (file_exists($manifest)) {
            $handle = fopen($manifest, 'r+');
            $content = fread($handle, filesize($manifest));
            fclose($handle);

            $this->manifest = json_decode($content);
        } else {
            $this->manifest = null;
            throw new NoManifestException('Unable to resolve the manifest.');
        }
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('resolve', [$this, 'resolveAsset']);
        $engine->registerFunction('asset', [$this, 'resolveAsset']);
    }

    /**
     * ATTENTION: Cette fonction ne devrais JAMAIS être appelé avec des paramètres contrôlés
     * par l'utilisateur.
     * @param string $assetToResolve
     * @return string
     * @throws AssetResolutionException
     */
    public function resolveAsset(string $assetToResolve): string
    {
        $simpleAssetsTheoreticalPath = realpath($this->simpleAssetsPath . self::DS . $assetToResolve);
        if (file_exists($simpleAssetsTheoreticalPath)) {
            return $this->simpleAssetsPublicPath . '/' . $assetToResolve;
        }

        $packedAssetsTheoreticalPath = realpath($this->packedAssetsPath . self::DS . $assetToResolve);
        if (file_exists($packedAssetsTheoreticalPath)) {
            return $this->packedAssetsPublicPath . '/' . $assetToResolve;
        }

        $manifestResolution = $this->manifestResolution($assetToResolve);
        if ($manifestResolution) {
            return $manifestResolution;
        }

        throw new AssetResolutionException('Unable to resolve the "' . $assetToResolve . '" asset.');
    }

    private function manifestResolution(string $filename): ?string
    {
        if ($this->manifest) {
            if (is_object($this->manifest) AND isset($this->manifest->$filename)) {
                return $this->manifest->$filename;
            } elseif (is_array($this->manifest) AND isset($this->manifest[$filename])) {
                return $this->manifest[$filename];
            }
        }
        return null;
    }
}
