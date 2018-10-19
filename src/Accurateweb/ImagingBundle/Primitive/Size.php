<?php
namespace Accurateweb\ImagingBundle\Primitive;

class Size
{
  private $width;
  private $height;

  public function __construct($width = 0, $height = 0)
  {
    $this->setWidth($width);
    $this->setHeight($height);
  }

  /**
   * Создает размер из строки вида %ширина%<разделитель>%высота%.
   *
   * По умолчанию в качестве разделителя используется "x". Если в строке в качестве
   * разделителя используется другой символ, передайте его вторым параметром
   *
   * Примеры:
   *
   * $size = Size::fromString('90x60');<br/>
   * $size = Size::fromString('90:60', ':');
   *
   * @param String $v Строковое представление размера
   * @param String $delimiter Разделитель
   * @return Size             Размер
   * @throws InvalidArgumentException
   */
  static public function fromString($v, $delimiter = 'x')
  {
    $parts = explode($delimiter, $v);
    if (count($parts) != 2)
      throw new \InvalidArgumentException(sprintf('Invalid size string "%s"', $v));

    if (!is_numeric($parts[0]) && !is_numeric($parts[1]))
      throw new \InvalidArgumentException(sprintf('Size components must be valid numbers'));

    return new Size($parts[0], $parts[1]);
  }

  /**
   * Создает размер из экзепляра класса sfImage
   *
   * @param sfImage $image Изображение
   * @return Size Размер указанного изображения
   */
  static public function fromImage(\Accurateweb\ImagingBundle\Image\Image $image)
  {
    return new Size($image->getWidth(), $image->getHeight());
  }

  /**
   * Если ширина или длина текущего размера равны нулю, дополняет их соответствующими значениями передаваемого размера
   *
   * @param Size $size
   * @return Size Возвращает себя для замыкания
   */
  public function extend(Size $size)
  {
    if ($this->width == 0)
      $this->width = $size->getWidth();
    if ($this->height == 0)
      $this->height = $size->getHeight();

    return $this;
  }

  public function getWidth()
  {
    return $this->width;
  }

  public function setWidth($v)
  {
    $this->width = (int)$v;
  }

  public function getHeight()
  {
    return $this->height;
  }

  public function setHeight($v)
  {
    $this->height = (int)$v;
  }

  /**
   * Возвращает соотношение длин в
   *
   * @return float|boolean
   */
  public function getAspectRatio()
  {
    if ($this->height == 0 || $this->width == 0)
      return false;

    return $this->width / $this->height;
  }
}
