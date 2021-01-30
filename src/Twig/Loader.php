<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Twig;

use App\Traits\TwigLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewFinderInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Basic loader using absolute paths.
 */
class Loader implements LoaderInterface
{
    use TwigLoader;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var string twig file extension
     */
    protected $extension;

    /**
     * @var array template lookup cache
     */
    protected $cache = [];

    /**
     * @param \Illuminate\Filesystem\Filesystem $files     The filesystem
     * @param string                            $extension twig file extension
     */
    public function __construct(Filesystem $files, ViewFinderInterface $finder, $extension = 'twig')
    {
        $this->files = $files;
        $this->finder = $finder;
        $this->extension = $extension;
    }

    /**
     * Normalize the Twig template name to a name the ViewFinder can use.
     *
     * @param string $name template file name
     *
     * @return string The parsed name
     */
    protected function normalizeName($name)
    {
        if ($this->files->extension($name) === $this->extension) {
            $name = substr($name, 0, -(strlen($this->extension) + 1));
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $this->findTemplate($name);
        } catch (LoaderError $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext(string $name): Source
    {
        $path = $this->findTemplate($name);

        return new Source($this->files->get($path), $name, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(string $name, int $time): bool
    {
        return $this->files->lastModified($this->findTemplate($name)) <= $time;
    }
}
