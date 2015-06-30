<?php

namespace BetterReflection\Reflection;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property as PropertyNode;
use phpDocumentor\Reflection\Type;
use BetterReflection\TypesFinder\TypesFinder;

class ReflectionProperty implements \Reflector
{
    const IS_PUBLIC = 1;
    const IS_PROTECTED = 2;
    const IS_PRIVATE = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $visibility;

    /**
     * @var bool
     */
    private $isStatic;

    /**
     * @var Type[]
     */
    private $types;

    /**
     * @var ReflectionClass
     */
    private $declaringClass;

    private function __construct()
    {
    }

    public static function export()
    {
        throw new \Exception('Unable to export statically');
    }

    /**
     * Return string representation of this little old property
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'Property [ <default> %s $%s ]',
            $this->getVisibilityAsString(),
            $this->getName()
        );
    }

    /**
     * @param PropertyNode $node
     * @param ReflectionClass $declaringClass
     * @return ReflectionProperty
     */
    public static function createFromNode(
        PropertyNode $node,
        ReflectionClass $declaringClass
    ) {
        $prop = new self();
        $prop->name = $node->props[0]->name;
        $prop->declaringClass = $declaringClass;

        if ($node->isPrivate()) {
            $prop->visibility = self::IS_PRIVATE;
        } else if ($node->isProtected()) {
            $prop->visibility = self::IS_PROTECTED;
        } else {
            $prop->visibility = self::IS_PUBLIC;
        }

        $prop->isStatic = $node->isStatic();

        $prop->types = TypesFinder::findTypeForProperty($node, $prop);

        return $prop;
    }

    /**
     * @return string
     */
    private function getVisibilityAsString()
    {
        switch($this->visibility)
        {
            case self::IS_PROTECTED:
                return 'protected';
            case self::IS_PRIVATE:
                return 'private';
            default:
                return 'public';
        }
    }

    /**
     * Get the name of the property
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Is the property private?
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->visibility == self::IS_PRIVATE;
    }

    /**
     * Is the property protected?
     *
     * @return bool
     */
    public function isProtected()
    {
        return $this->visibility == self::IS_PROTECTED;
    }

    /**
     * Is the property public?
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->visibility == self::IS_PUBLIC;
    }

    /**
     * Is the property static?
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * @return string[]
     */
    public function getTypeStrings()
    {
        $stringTypes = [];

        foreach ($this->types as $type) {
            $stringTypes[] = (string)$type;
        }
        return $stringTypes;
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }
}
