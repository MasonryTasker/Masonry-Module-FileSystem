<?php
/**
 * Description.php
 * PHP version 5.4
 * 2015-09-30
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\FileNotExists;

use Foundry\Masonry\Core\AbstractDescription;

/**
 * Class Description
 *
 * @package Foundry\Masonry-Website-Builder
 */
class Description extends AbstractDescription
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name The name of the file
     */
    public function __construct($name)
    {
        if (!$name) {
            throw new \InvalidArgumentException('$name is required');
        }
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
