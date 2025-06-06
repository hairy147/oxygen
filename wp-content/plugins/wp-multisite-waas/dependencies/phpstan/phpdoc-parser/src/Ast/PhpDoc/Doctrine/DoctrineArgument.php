<?php

declare (strict_types=1);
namespace WP_Ultimo\Dependencies\PHPStan\PhpDocParser\Ast\PhpDoc\Doctrine;

use WP_Ultimo\Dependencies\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use WP_Ultimo\Dependencies\PHPStan\PhpDocParser\Ast\Node;
use WP_Ultimo\Dependencies\PHPStan\PhpDocParser\Ast\NodeAttributes;
use WP_Ultimo\Dependencies\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
/**
 * @phpstan-type ValueType = DoctrineAnnotation|IdentifierTypeNode|DoctrineArray|ConstExprNode
 */
class DoctrineArgument implements Node
{
    use NodeAttributes;
    /** @var IdentifierTypeNode|null */
    public $key;
    /** @var ValueType */
    public $value;
    /**
     * @param ValueType $value
     */
    public function __construct(?IdentifierTypeNode $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
    public function __toString() : string
    {
        if ($this->key === null) {
            return (string) $this->value;
        }
        return $this->key . '=' . $this->value;
    }
}
