<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Symfony\Component\Finder;

use GravityKit\GravityView\Symfony\Component\Finder\Comparator\DateComparator;
use GravityKit\GravityView\Symfony\Component\Finder\Comparator\NumberComparator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\CustomFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\DateRangeFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\DepthRangeFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\FilecontentFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\FilenameFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\SizeRangeFilterIterator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\SortableIterator;

/**
 * Finder allows to build rules to find files and directories.
 *
 * It is a thin wrapper around several specialized iterator classes.
 *
 * All rules may be invoked several times.
 *
 * All methods return the current Finder object to allow chaining:
 *
 *     $finder = Finder::create()->files()->name('*.php')->in(__DIR__);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Finder implements \IteratorAggregate, \Countable
{
    const IGNORE_VCS_FILES = 1;
    const IGNORE_DOT_FILES = 2;

    private $mode = 0;
    private $names = [];
    private $notNames = [];
    private $exclude = [];
    private $filters = [];
    private $depths = [];
    private $sizes = [];
    private $followLinks = false;
    private $sort = false;
    private $ignore = 0;
    private $dirs = [];
    private $dates = [];
    private $iterators = [];
    private $contains = [];
    private $notContains = [];
    private $paths = [];
    private $notPaths = [];
    private $ignoreUnreadableDirs = false;

    private static $vcsPatterns = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg'];

    public function __construct()
    {
        $this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;
    }

    /**
     * Creates a new Finder.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Restricts the matching to directories only.
     *
     * @return $this
     */
    public function directories()
    {
        $this->mode = Iterator\FileTypeFilterIterator::ONLY_DIRECTORIES;

        return $this;
    }

    /**
     * Restricts the matching to files only.
     *
     * @return $this
     */
    public function files()
    {
        $this->mode = Iterator\FileTypeFilterIterator::ONLY_FILES;

        return $this;
    }

    /**
     * Adds tests for the directory depth.
     *
     * Usage:
     *
     *     $finder->depth('> 1') // the Finder will start matching at level 1.
     *     $finder->depth('< 3') // the Finder will descend at most 3 levels of directories below the starting point.
     *
     * @param string|int $level The depth level expression
     *
     * @return $this
     *
     * @see DepthRangeFilterIterator
     * @see NumberComparator
     */
    public function depth($level)
    {
        $this->depths[] = new Comparator\NumberComparator($level);

        return $this;
    }

    /**
     * Adds tests for file dates (last modified).
     *
     * The date must be something that strtotime() is able to parse:
     *
     *     $finder->date('since yesterday');
     *     $finder->date('until 2 days ago');
     *     $finder->date('> now - 2 hours');
     *     $finder->date('>= 2005-10-15');
     *
     * @param string $date A date range string
     *
     * @return $this
     *
     * @see strtotime
     * @see DateRangeFilterIterator
     * @see DateComparator
     */
    public function date($date)
    {
        $this->dates[] = new Comparator\DateComparator($date);

        return $this;
    }

    /**
     * Adds rules that files must match.
     *
     * You can use patterns (delimited with / sign), globs or simple strings.
     *
     *     $finder->name('*.php')
     *     $finder->name('/\.php$/') // same as above
     *     $finder->name('test.php')
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function name($pattern)
    {
        $this->names[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that files must not match.
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function notName($pattern)
    {
        $this->notNames[] = $pattern;

        return $this;
    }

    /**
     * Adds tests that file contents must match.
     *
     * Strings or PCRE patterns can be used:
     *
     *     $finder->contains('Lorem ipsum')
     *     $finder->contains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return $this
     *
     * @see FilecontentFilterIterator
     */
    public function contains($pattern)
    {
        $this->contains[] = $pattern;

        return $this;
    }

    /**
     * Adds tests that file contents must not match.
     *
     * Strings or PCRE patterns can be used:
     *
     *     $finder->notContains('Lorem ipsum')
     *     $finder->notContains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return $this
     *
     * @see FilecontentFilterIterator
     */
    public function notContains($pattern)
    {
        $this->notContains[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that filenames must match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     *     $finder->path('some/special/dir')
     *     $finder->path('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function path($pattern)
    {
        $this->paths[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that filenames must not match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     *     $finder->notPath('some/special/dir')
     *     $finder->notPath('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function notPath($pattern)
    {
        $this->notPaths[] = $pattern;

        return $this;
    }

    /**
     * Adds tests for file sizes.
     *
     *     $finder->size('> 10K');
     *     $finder->size('<= 1Ki');
     *     $finder->size(4);
     *
     * @param string|int $size A size range string or an integer
     *
     * @return $this
     *
     * @see SizeRangeFilterIterator
     * @see NumberComparator
     */
    public function size($size)
    {
        $this->sizes[] = new Comparator\NumberComparator($size);

        return $this;
    }

    /**
     * Excludes directories.
     *
     * Directories passed as argument must be relative to the ones defined with the `in()` method. For example:
     *
     *     $finder->in(__DIR__)->exclude('ruby');
     *
     * @param string|array $dirs A directory path or an array of directories
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function exclude($dirs)
    {
        $this->exclude = array_merge($this->exclude, (array) $dirs);

        return $this;
    }

    /**
     * Excludes "hidden" directories and files (starting with a dot).
     *
     * This option is enabled by default.
     *
     * @param bool $ignoreDotFiles Whether to exclude "hidden" files or not
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function ignoreDotFiles($ignoreDotFiles)
    {
        if ($ignoreDotFiles) {
            $this->ignore |= static::IGNORE_DOT_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_DOT_FILES;
        }

        return $this;
    }

    /**
     * Forces the finder to ignore version control directories.
     *
     * This option is enabled by default.
     *
     * @param bool $ignoreVCS Whether to exclude VCS files or not
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function ignoreVCS($ignoreVCS)
    {
        if ($ignoreVCS) {
            $this->ignore |= static::IGNORE_VCS_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_VCS_FILES;
        }

        return $this;
    }

    /**
     * Adds VCS patterns.
     *
     * @see ignoreVCS()
     *
     * @param string|string[] $pattern VCS patterns to ignore
     */
    public static function addVCSPattern($pattern)
    {
        foreach ((array) $pattern as $p) {
            self::$vcsPatterns[] = $p;
        }

        self::$vcsPatterns = array_unique(self::$vcsPatterns);
    }

    /**
     * Sorts files and directories by an anonymous function.
     *
     * The anonymous function receives two \SplFileInfo instances to compare.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sort(\Closure $closure)
    {
        $this->sort = $closure;

        return $this;
    }

    /**
     * Sorts files and directories by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByName()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_NAME;

        return $this;
    }

    /**
     * Sorts files and directories by type (directories before files), then by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByType()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_TYPE;

        return $this;
    }

    /**
     * Sorts files and directories by the last accessed time.
     *
     * This is the time that the file was last accessed, read or written to.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByAccessedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_ACCESSED_TIME;

        return $this;
    }

    /**
     * Sorts files and directories by the last inode changed time.
     *
     * This is the time that the inode information was last modified (permissions, owner, group or other metadata).
     *
     * On Windows, since inode is not available, changed time is actually the file creation time.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByChangedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_CHANGED_TIME;

        return $this;
    }

    /**
     * Sorts files and directories by the last modified time.
     *
     * This is the last time the actual contents of the file were last modified.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByModifiedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_MODIFIED_TIME;

        return $this;
    }

    /**
     * Filters the iterator with an anonymous function.
     *
     * The anonymous function receives a \SplFileInfo and must return false
     * to remove files.
     *
     * @return $this
     *
     * @see CustomFilterIterator
     */
    public function filter(\Closure $closure)
    {
        $this->filters[] = $closure;

        return $this;
    }

    /**
     * Forces the following of symlinks.
     *
     * @return $this
     */
    public function followLinks()
    {
        $this->followLinks = true;

        return $this;
    }

    /**
     * Tells finder to ignore unreadable directories.
     *
     * By default, scanning unreadable directories content throws an AccessDeniedException.
     *
     * @param bool $ignore
     *
     * @return $this
     */
    public function ignoreUnreadableDirs($ignore = true)
    {
        $this->ignoreUnreadableDirs = (bool) $ignore;

        return $this;
    }

    /**
     * Searches files and directories which match defined rules.
     *
     * @param string|string[] $dirs A directory path or an array of directories
     *
     * @return $this
     *
     * @throws \InvalidArgumentException if one of the directories does not exist
     */
    public function in($dirs)
    {
        $resolvedDirs = [];

        foreach ((array) $dirs as $dir) {
            if (is_dir($dir)) {
                $resolvedDirs[] = $this->normalizeDir($dir);
            } elseif ($glob = glob($dir, (\defined('GLOB_BRACE') ? \GLOB_BRACE : 0) | \GLOB_ONLYDIR | \GLOB_NOSORT)) {
                sort($glob);
                $resolvedDirs = array_merge($resolvedDirs, array_map([$this, 'normalizeDir'], $glob));
            } else {
                throw new \InvalidArgumentException(sprintf('The "%s" directory does not exist.', $dir));
            }
        }

        $this->dirs = array_merge($this->dirs, $resolvedDirs);

        return $this;
    }

    /**
     * Returns an Iterator for the current Finder configuration.
     *
     * This method implements the IteratorAggregate interface.
     *
     * @return \Iterator|SplFileInfo[] An iterator
     *
     * @throws \LogicException if the in() method has not been called
     */
    public function getIterator()
    {
        if (0 === \count($this->dirs) && 0 === \count($this->iterators)) {
            throw new \LogicException('You must call one of in() or append() methods before iterating over a Finder.');
        }

        if (1 === \count($this->dirs) && 0 === \count($this->iterators)) {
            return $this->searchInDirectory($this->dirs[0]);
        }

        $iterator = new \AppendIterator();
        foreach ($this->dirs as $dir) {
            $iterator->append($this->searchInDirectory($dir));
        }

        foreach ($this->iterators as $it) {
            $iterator->append($it);
        }

        return $iterator;
    }

    /**
     * Appends an existing set of files/directories to the finder.
     *
     * The set can be another Finder, an Iterator, an IteratorAggregate, or even a plain array.
     *
     * @param iterable $iterator
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when the given argument is not iterable
     */
    public function append($iterator)
    {
        if ($iterator instanceof \IteratorAggregate) {
            $this->iterators[] = $iterator->getIterator();
        } elseif ($iterator instanceof \Iterator) {
            $this->iterators[] = $iterator;
        } elseif ($iterator instanceof \Traversable || \is_array($iterator)) {
            $it = new \ArrayIterator();
            foreach ($iterator as $file) {
                $it->append($file instanceof \SplFileInfo ? $file : new \SplFileInfo($file));
            }
            $this->iterators[] = $it;
        } else {
            throw new \InvalidArgumentException('Finder::append() method wrong argument type.');
        }

        return $this;
    }

    /**
     * Check if any results were found.
     *
     * @return bool
     */
    public function hasResults()
    {
        foreach ($this->getIterator() as $_) {
            return true;
        }

        return false;
    }

    /**
     * Counts all the results collected by the iterators.
     *
     * @return int
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }

    /**
     * @param string $dir
     *
     * @return \Iterator
     */
    private function searchInDirectory($dir)
    {
        $exclude = $this->exclude;
        $notPaths = $this->notPaths;

        if (static::IGNORE_VCS_FILES === (static::IGNORE_VCS_FILES & $this->ignore)) {
            $exclude = array_merge($exclude, self::$vcsPatterns);
        }

        if (static::IGNORE_DOT_FILES === (static::IGNORE_DOT_FILES & $this->ignore)) {
            $notPaths[] = '#(^|/)\..+(/|$)#';
        }

        $minDepth = 0;
        $maxDepth = \PHP_INT_MAX;

        foreach ($this->depths as $comparator) {
            switch ($comparator->getOperator()) {
                case '>':
                    $minDepth = $comparator->getTarget() + 1;
                    break;
                case '>=':
                    $minDepth = $comparator->getTarget();
                    break;
                case '<':
                    $maxDepth = $comparator->getTarget() - 1;
                    break;
                case '<=':
                    $maxDepth = $comparator->getTarget();
                    break;
                default:
                    $minDepth = $maxDepth = $comparator->getTarget();
            }
        }

        $flags = \RecursiveDirectoryIterator::SKIP_DOTS;

        if ($this->followLinks) {
            $flags |= \RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        }

        $iterator = new Iterator\RecursiveDirectoryIterator($dir, $flags, $this->ignoreUnreadableDirs);

        if ($exclude) {
            $iterator = new Iterator\ExcludeDirectoryFilterIterator($iterator, $exclude);
        }

        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

        if ($minDepth > 0 || $maxDepth < \PHP_INT_MAX) {
            $iterator = new Iterator\DepthRangeFilterIterator($iterator, $minDepth, $maxDepth);
        }

        if ($this->mode) {
            $iterator = new Iterator\FileTypeFilterIterator($iterator, $this->mode);
        }

        if ($this->names || $this->notNames) {
            $iterator = new Iterator\FilenameFilterIterator($iterator, $this->names, $this->notNames);
        }

        if ($this->contains || $this->notContains) {
            $iterator = new Iterator\FilecontentFilterIterator($iterator, $this->contains, $this->notContains);
        }

        if ($this->sizes) {
            $iterator = new Iterator\SizeRangeFilterIterator($iterator, $this->sizes);
        }

        if ($this->dates) {
            $iterator = new Iterator\DateRangeFilterIterator($iterator, $this->dates);
        }

        if ($this->filters) {
            $iterator = new Iterator\CustomFilterIterator($iterator, $this->filters);
        }

        if ($this->paths || $notPaths) {
            $iterator = new Iterator\PathFilterIterator($iterator, $this->paths, $notPaths);
        }

        if ($this->sort) {
            $iteratorAggregate = new Iterator\SortableIterator($iterator, $this->sort);
            $iterator = $iteratorAggregate->getIterator();
        }

        return $iterator;
    }

    /**
     * Normalizes given directory names by removing trailing slashes.
     *
     * Excluding: (s)ftp:// or ssh2.(s)ftp:// wrapper
     *
     * @param string $dir
     *
     * @return string
     */
    private function normalizeDir($dir)
    {
        if ('/' === $dir) {
            return $dir;
        }

        $dir = rtrim($dir, '/'.\DIRECTORY_SEPARATOR);

        if (preg_match('#^(ssh2\.)?s?ftp://#', $dir)) {
            $dir .= '/';
        }

        return $dir;
    }
}
