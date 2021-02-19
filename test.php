<?php

interface Shape
{
  public function getArea(): int;
}

class Rectangle implements Shape
{
  private $width = 0;
  private $height = 0;

  public function __construct(int $width, int $height)
  {
    $this->width = $width;
    $this->height = $height;
  }

  public function getArea(): int
  {
    return $this->width * $this->height;
  }
}

class Square implements Shape
{
  private $length = 0;

  public function __construct(int $length)
  {
    $this->length = $length;
  }

  public function getArea(): int
  {
    return $this->length ** 2;
  }
}

class triangle implements Shape
{
  private $height = 0;
  private $bottom = 0;

  public function __construct(int $bottom, int $height)
  {
    $this->height = $height;
    $this->bottom = $bottom;
  }
  public function getArea(): int
  {
    return $this->bottom * $this->height / 2;
  }
}

function printArea(Shape $shape): void
{
  echo sprintf('%s has area %d.', get_class($shape), $shape->getArea()) . PHP_EOL;
}

$shapes = [new Rectangle(4, 5), new Square(5), new Triangle(5, 4)];

// foreach ($shapes as $shape) {
//   printArea($shape);
// }

// echo '1jdf' === 1 ? 'yes' : 'no' . PHP_EOL;
phpinfo();
