<?php

namespace StoreBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateIntervalType extends AbstractType implements DataTransformerInterface
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addViewTransformer($this);
  }
  /**
   * {@inheritdoc}
   */
  public function reverseTransform($data)
  {
    if (is_string($data) && preg_match('/^\d{2}\.\d{2}\.\d{4}\s*\-\s*\d{2}\.\d{2}\.\d{4}$/', $data))
    {
      $data = explode('-', $data);
      $dateFrom = new \DateTime($data[0]);
      $dateTo = new \DateTime($data[1]);

      return [
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
      ];
    }

    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($data)
  {
    if (is_array($data) && isset($data['dateFrom']) && isset($data['dateTo'])
      && $data['dateFrom'] instanceof \DateTime
      && $data['dateTo'] instanceof \DateTime
    )
    {
      $data = sprintf('%s - %s', $data['dateFrom']->format('d.m.Y'), $data['dateTo']->format('d.m.Y'));
    }

    if (!is_string($data))
    {
      return '';
    }

    return $data;
  }

  public function getBlockPrefix ()
  {
    return 'custom_date_interval';
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'compound' => false,
    ));
  }
}