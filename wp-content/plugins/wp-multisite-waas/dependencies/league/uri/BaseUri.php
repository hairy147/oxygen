<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace WP_Ultimo\Dependencies\League\Uri;

use JsonSerializable;
use WP_Ultimo\Dependencies\League\Uri\Contracts\UriAccess;
use WP_Ultimo\Dependencies\League\Uri\Contracts\UriInterface;
use WP_Ultimo\Dependencies\League\Uri\Exceptions\MissingFeature;
use WP_Ultimo\Dependencies\League\Uri\Idna\Converter;
use WP_Ultimo\Dependencies\League\Uri\IPv4\Converter as IPv4Converter;
use WP_Ultimo\Dependencies\Psr\Http\Message\UriFactoryInterface;
use WP_Ultimo\Dependencies\Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;
use function array_pop;
use function array_reduce;
use function count;
use function end;
use function explode;
use function implode;
use function str_repeat;
use function strpos;
use function substr;
/**
 * @phpstan-import-type ComponentMap from UriInterface
 */
class BaseUri implements Stringable, JsonSerializable, UriAccess
{
    /** @var array<string,int> */
    protected final const WHATWG_SPECIAL_SCHEMES = ['ftp' => 1, 'http' => 1, 'https' => 1, 'ws' => 1, 'wss' => 1];
    /** @var array<string,int> */
    protected final const DOT_SEGMENTS = ['.' => 1, '..' => 1];
    protected readonly Psr7UriInterface|UriInterface|null $origin;
    protected readonly ?string $nullValue;
    protected final function __construct(protected readonly Psr7UriInterface|UriInterface $uri, protected readonly ?UriFactoryInterface $uriFactory)
    {
        $this->nullValue = $this->uri instanceof Psr7UriInterface ? '' : null;
        $this->origin = $this->computeOrigin($this->uri, $this->nullValue);
    }
    public static function from(Stringable|string $uri, ?UriFactoryInterface $uriFactory = null) : static
    {
        return new static(static::formatHost(static::filterUri($uri, $uriFactory)), $uriFactory);
    }
    public function withUriFactory(UriFactoryInterface $uriFactory) : static
    {
        return new static($this->uri, $uriFactory);
    }
    public function withoutUriFactory() : static
    {
        return new static($this->uri, null);
    }
    public function getUri() : Psr7UriInterface|UriInterface
    {
        return $this->uri;
    }
    public function getUriString() : string
    {
        return $this->uri->__toString();
    }
    public function jsonSerialize() : string
    {
        return $this->uri->__toString();
    }
    public function __toString() : string
    {
        return $this->uri->__toString();
    }
    public function origin() : ?self
    {
        return match (null) {
            $this->origin => null,
            default => new self($this->origin, $this->uriFactory),
        };
    }
    /**
     * Tells whether two URI do not share the same origin.
     */
    public function isCrossOrigin(Stringable|string $uri) : bool
    {
        if (null === $this->origin) {
            return \true;
        }
        $uri = static::filterUri($uri);
        $uriOrigin = $this->computeOrigin($uri, $uri instanceof Psr7UriInterface ? '' : null);
        return match (\true) {
            null === $uriOrigin, $uriOrigin->__toString() !== $this->origin->__toString() => \true,
            default => \false,
        };
    }
    /**
     * Tells whether the URI is absolute.
     */
    public function isAbsolute() : bool
    {
        return $this->nullValue !== $this->uri->getScheme();
    }
    /**
     * Tells whether the URI is a network path.
     */
    public function isNetworkPath() : bool
    {
        return $this->nullValue === $this->uri->getScheme() && $this->nullValue !== $this->uri->getAuthority();
    }
    /**
     * Tells whether the URI is an absolute path.
     */
    public function isAbsolutePath() : bool
    {
        return $this->nullValue === $this->uri->getScheme() && $this->nullValue === $this->uri->getAuthority() && '/' === ($this->uri->getPath()[0] ?? '');
    }
    /**
     * Tells whether the URI is a relative path.
     */
    public function isRelativePath() : bool
    {
        return $this->nullValue === $this->uri->getScheme() && $this->nullValue === $this->uri->getAuthority() && '/' !== ($this->uri->getPath()[0] ?? '');
    }
    /**
     * Tells whether both URI refers to the same document.
     */
    public function isSameDocument(Stringable|string $uri) : bool
    {
        return $this->normalize(static::filterUri($uri)) === $this->normalize($this->uri);
    }
    /**
     * Tells whether the URI contains an Internationalized Domain Name (IDN).
     */
    public function hasIdn() : bool
    {
        return Converter::isIdn($this->uri->getHost());
    }
    /**
     * Resolves a URI against a base URI using RFC3986 rules.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter or silence them apart from validating its own parameters.
     */
    public function resolve(Stringable|string $uri) : static
    {
        $uri = static::formatHost(static::filterUri($uri, $this->uriFactory));
        $null = $uri instanceof Psr7UriInterface ? '' : null;
        if ($null !== $uri->getScheme()) {
            return new static($uri->withPath(static::removeDotSegments($uri->getPath())), $this->uriFactory);
        }
        if ($null !== $uri->getAuthority()) {
            return new static($uri->withScheme($this->uri->getScheme())->withPath(static::removeDotSegments($uri->getPath())), $this->uriFactory);
        }
        $user = $null;
        $pass = null;
        $userInfo = $this->uri->getUserInfo();
        if (null !== $userInfo) {
            [$user, $pass] = explode(':', $userInfo, 2) + [1 => null];
        }
        [$path, $query] = $this->resolvePathAndQuery($uri);
        return new static($uri->withPath($this->removeDotSegments($path))->withQuery($query)->withHost($this->uri->getHost())->withPort($this->uri->getPort())->withUserInfo((string) $user, $pass)->withScheme($this->uri->getScheme()), $this->uriFactory);
    }
    /**
     * Relativize a URI according to a base URI.
     *
     * This method MUST retain the state of the submitted URI instance, and return
     * a URI instance of the same type that contains the applied modifications.
     *
     * This method MUST be transparent when dealing with error and exceptions.
     * It MUST not alter of silence them apart from validating its own parameters.
     */
    public function relativize(Stringable|string $uri) : static
    {
        $uri = static::formatHost(static::filterUri($uri, $this->uriFactory));
        if ($this->canNotBeRelativize($uri)) {
            return new static($uri, $this->uriFactory);
        }
        $null = $uri instanceof Psr7UriInterface ? '' : null;
        $uri = $uri->withScheme($null)->withPort(null)->withUserInfo($null)->withHost($null);
        $targetPath = $uri->getPath();
        $basePath = $this->uri->getPath();
        return new static(match (\true) {
            $targetPath !== $basePath => $uri->withPath(static::relativizePath($targetPath, $basePath)),
            static::componentEquals('query', $uri) => $uri->withPath('')->withQuery($null),
            $null === $uri->getQuery() => $uri->withPath(static::formatPathWithEmptyBaseQuery($targetPath)),
            default => $uri->withPath(''),
        }, $this->uriFactory);
    }
    protected final function computeOrigin(Psr7UriInterface|UriInterface $uri, ?string $nullValue) : Psr7UriInterface|UriInterface|null
    {
        $scheme = $uri->getScheme();
        if ('blob' !== $scheme) {
            return match (\true) {
                isset(static::WHATWG_SPECIAL_SCHEMES[$scheme]) => $uri->withFragment($nullValue)->withQuery($nullValue)->withPath('')->withUserInfo($nullValue),
                default => null,
            };
        }
        $components = UriString::parse($uri->getPath());
        if ($uri instanceof Psr7UriInterface) {
            /** @var ComponentMap $components */
            $components = \array_map(fn($component) => null === $component ? '' : $component, $components);
        }
        return match (\true) {
            null !== $components['scheme'] && isset(static::WHATWG_SPECIAL_SCHEMES[\strtolower($components['scheme'])]) => $uri->withFragment($nullValue)->withQuery($nullValue)->withPath('')->withHost($components['host'])->withPort($components['port'])->withScheme($components['scheme'])->withUserInfo($nullValue),
            default => null,
        };
    }
    /**
     * Normalizes a URI for comparison; this URI string representation is not suitable for usage as per RFC guidelines.
     */
    protected final function normalize(Psr7UriInterface|UriInterface $uri) : string
    {
        $null = $uri instanceof Psr7UriInterface ? '' : null;
        $path = $uri->getPath();
        if ('/' === ($path[0] ?? '') || '' !== $uri->getScheme() . $uri->getAuthority()) {
            $path = $this->removeDotSegments($path);
        }
        $query = $uri->getQuery();
        $pairs = null === $query ? [] : explode('&', $query);
        \sort($pairs);
        static $regexpEncodedChars = ',%(2[D|E]|3\\d|4[1-9|A-F]|5[\\d|AF]|6[1-9|A-F]|7[\\d|E]),i';
        $value = \preg_replace_callback($regexpEncodedChars, static fn(array $matches): string => \rawurldecode($matches[0]), [$path, implode('&', $pairs)]) ?? ['', $null];
        [$path, $query] = $value + ['', $null];
        if ($null !== $uri->getAuthority() && '' === $path) {
            $path = '/';
        }
        return $uri->withHost(Uri::fromComponents(['host' => $uri->getHost()])->getHost())->withPath($path)->withQuery([] === $pairs ? $null : $query)->withFragment($null)->__toString();
    }
    /**
     * Input URI normalization to allow Stringable and string URI.
     */
    protected static final function filterUri(Stringable|string $uri, UriFactoryInterface|null $uriFactory = null) : Psr7UriInterface|UriInterface
    {
        return match (\true) {
            $uri instanceof UriAccess => $uri->getUri(),
            $uri instanceof Psr7UriInterface, $uri instanceof UriInterface => $uri,
            $uriFactory instanceof UriFactoryInterface => $uriFactory->createUri((string) $uri),
            default => Uri::new($uri),
        };
    }
    /**
     * Remove dot segments from the URI path as per RFC specification.
     */
    protected final function removeDotSegments(string $path) : string
    {
        if (!\str_contains($path, '.')) {
            return $path;
        }
        $reducer = function (array $carry, string $segment) : array {
            if ('..' === $segment) {
                array_pop($carry);
                return $carry;
            }
            if (!isset(static::DOT_SEGMENTS[$segment])) {
                $carry[] = $segment;
            }
            return $carry;
        };
        $oldSegments = explode('/', $path);
        $newPath = implode('/', array_reduce($oldSegments, $reducer(...), []));
        if (isset(static::DOT_SEGMENTS[end($oldSegments)])) {
            $newPath .= '/';
        }
        // @codeCoverageIgnoreStart
        // added because some PSR-7 implementations do not respect RFC3986
        if (\str_starts_with($path, '/') && !\str_starts_with($newPath, '/')) {
            return '/' . $newPath;
        }
        // @codeCoverageIgnoreEnd
        return $newPath;
    }
    /**
     * Resolves an URI path and query component.
     *
     * @return array{0:string, 1:string|null}
     */
    protected final function resolvePathAndQuery(Psr7UriInterface|UriInterface $uri) : array
    {
        $targetPath = $uri->getPath();
        $null = $uri instanceof Psr7UriInterface ? '' : null;
        if (\str_starts_with($targetPath, '/')) {
            return [$targetPath, $uri->getQuery()];
        }
        if ('' === $targetPath) {
            $targetQuery = $uri->getQuery();
            if ($null === $targetQuery) {
                $targetQuery = $this->uri->getQuery();
            }
            $targetPath = $this->uri->getPath();
            //@codeCoverageIgnoreStart
            //because some PSR-7 Uri implementations allow this RFC3986 forbidden construction
            if (null !== $this->uri->getAuthority() && !\str_starts_with($targetPath, '/')) {
                $targetPath = '/' . $targetPath;
            }
            //@codeCoverageIgnoreEnd
            return [$targetPath, $targetQuery];
        }
        $basePath = $this->uri->getPath();
        if (null !== $this->uri->getAuthority() && '' === $basePath) {
            $targetPath = '/' . $targetPath;
        }
        if ('' !== $basePath) {
            $segments = explode('/', $basePath);
            array_pop($segments);
            if ([] !== $segments) {
                $targetPath = implode('/', $segments) . '/' . $targetPath;
            }
        }
        return [$targetPath, $uri->getQuery()];
    }
    /**
     * Tells whether the component value from both URI object equals.
     *
     * @pqram 'query'|'authority'|'scheme' $property
     */
    protected final function componentEquals(string $property, Psr7UriInterface|UriInterface $uri) : bool
    {
        $getComponent = function (string $property, Psr7UriInterface|UriInterface $uri) : ?string {
            $component = match ($property) {
                'query' => $uri->getQuery(),
                'authority' => $uri->getAuthority(),
                default => $uri->getScheme(),
            };
            return match (\true) {
                $uri instanceof UriInterface, '' !== $component => $component,
                default => null,
            };
        };
        return $getComponent($property, $uri) === $getComponent($property, $this->uri);
    }
    /**
     * Filter the URI object.
     */
    protected static final function formatHost(Psr7UriInterface|UriInterface $uri) : Psr7UriInterface|UriInterface
    {
        $host = $uri->getHost();
        try {
            $converted = IPv4Converter::fromEnvironment()->toDecimal($host);
        } catch (MissingFeature) {
            $converted = null;
        }
        return match (\true) {
            null !== $converted => $uri->withHost($converted),
            '' === $host, $uri instanceof UriInterface => $uri,
            default => $uri->withHost((string) Uri::fromComponents(['host' => $host])->getHost()),
        };
    }
    /**
     * Tells whether the submitted URI object can be relativized.
     */
    protected final function canNotBeRelativize(Psr7UriInterface|UriInterface $uri) : bool
    {
        return !static::componentEquals('scheme', $uri) || !static::componentEquals('authority', $uri) || static::from($uri)->isRelativePath();
    }
    /**
     * Relatives the URI for an authority-less target URI.
     */
    protected static final function relativizePath(string $path, string $basePath) : string
    {
        $baseSegments = static::getSegments($basePath);
        $targetSegments = static::getSegments($path);
        $targetBasename = array_pop($targetSegments);
        array_pop($baseSegments);
        foreach ($baseSegments as $offset => $segment) {
            if (!isset($targetSegments[$offset]) || $segment !== $targetSegments[$offset]) {
                break;
            }
            unset($baseSegments[$offset], $targetSegments[$offset]);
        }
        $targetSegments[] = $targetBasename;
        return static::formatPath(str_repeat('../', count($baseSegments)) . implode('/', $targetSegments), $basePath);
    }
    /**
     * returns the path segments.
     *
     * @return string[]
     */
    protected static final function getSegments(string $path) : array
    {
        return explode('/', match (\true) {
            '' === $path, '/' !== $path[0] => $path,
            default => substr($path, 1),
        });
    }
    /**
     * Formatting the path to keep a valid URI.
     */
    protected static final function formatPath(string $path, string $basePath) : string
    {
        $colonPosition = strpos($path, ':');
        $slashPosition = strpos($path, '/');
        return match (\true) {
            '' === $path => match (\true) {
                '' === $basePath, '/' === $basePath => $basePath,
                default => './',
            },
            \false === $colonPosition => $path,
            \false === $slashPosition, $colonPosition < $slashPosition => "./{$path}",
            default => $path,
        };
    }
    /**
     * Formatting the path to keep a resolvable URI.
     */
    protected static final function formatPathWithEmptyBaseQuery(string $path) : string
    {
        $targetSegments = static::getSegments($path);
        /** @var string $basename */
        $basename = end($targetSegments);
        return '' === $basename ? './' : $basename;
    }
}
