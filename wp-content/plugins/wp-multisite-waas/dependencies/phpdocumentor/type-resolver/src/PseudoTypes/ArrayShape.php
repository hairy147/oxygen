<?php

/*
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */
declare (strict_types=1);
namespace WP_Ultimo\Dependencies\phpDocumentor\Reflection\PseudoTypes;

use WP_Ultimo\Dependencies\phpDocumentor\Reflection\PseudoType;
use WP_Ultimo\Dependencies\phpDocumentor\Reflection\Type;
use WP_Ultimo\Dependencies\phpDocumentor\Reflection\Types\Array_;
use WP_Ultimo\Dependencies\phpDocumentor\Reflection\Types\ArrayKey;
use WP_Ultimo\Dependencies\phpDocumentor\Reflection\Types\Mixed_;
use function implode;
/** @psalm-immutable */
class ArrayShape implements PseudoType
{
    /** @var ArrayShapeItem[] */
    private array $items;
    public function __construct(ArrayShapeItem ...$items)
    {
        $this->items = $items;
    }
    public function underlyingType() : Type
    {
        return new Array_(new Mixed_(), new ArrayKey());
    }
    public function __toString() : string
    {
        return 'array{' . implode(', ', $this->items) . '}';
    }
}
