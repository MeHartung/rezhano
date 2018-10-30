<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller\Catalog;


use StoreBundle\Entity\Store\Catalog\Product\ProductQuestion;
use StoreBundle\Form\Catalog\Product\ProductQuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends Controller
{
  /**
   * Страница списка товаров (/products).
   *
   * Всегда перенаправляет в каталог.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function indexAction()
  {
    return $this->redirectToRoute('catalog_index', array(), 301);
  }

  /**
   * Контроллер страницы товара
   *
   * @param $slug
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function showAction($slug)
  {
    $product = $this->getDoctrine()
                    ->getRepository('StoreBundle:Store\Catalog\Product\Product')
                    ->findOneBy(array('slug' => $slug));

    if (!$product)
    {
      throw $this->createNotFoundException(sprintf('Товар "%s" не найден', $slug));
    }

    if (!$this->isGranted('publication', $product))
    {
      throw $this->createNotFoundException(sprintf('Товар "%s" снят с публикации', $slug));
    }

    
    if($product->getTaxons()->count() === 0)
    {
      throw $this->createNotFoundException(sprintf('Товар "%s" не найден', $slug));
    }
    
    $taxon = $product->getTaxons()->first();
    $productAttributeValues = $product->getProductAttributeValues();

    if (!empty($productAttributeValues) && count($productAttributeValues))
    {
      $productAttributeValues = $productAttributeValues->toArray();
      $productAttributeValueChunks = array_chunk($productAttributeValues, ceil(count($productAttributeValues) / 2));
    }
    else
    {
      $productAttributeValueChunks = [];
    }

    return $this->render('StoreBundle:Catalog\Product:show.html.twig', array(
      'product' => $product,
      'productAttributeValueChunks' => $productAttributeValueChunks,
      'taxon' => $taxon,
      'stockManager' => $this->get('aw.logistic.stock.manager'),
    ));
  }

  public function restGetAction($slug)
  {
    $product = $this->getDoctrine()
      ->getRepository('StoreBundle:Store\Catalog\Product\Product')
      ->findOneBy(array('slug' => $slug));

    if (!$product)
    {
      throw $this->createNotFoundException(sprintf('Товар "%s" не найден', $slug));
    }

    $imageUrl = null;
    $mainImage = $product->getMainImage();

    if ($mainImage)
    {
      $mainImageResource = $this->get('aw.media.manager')->getMediaStorage($mainImage)->retrieve($mainImage);

      if ($mainImageResource)
      {
        $imageUrl = $mainImageResource->getUrl();
      }
    }

    $adapter = $this->get('store.factory.product_client_adapter')->getModelAdapter($product);
    $productJson = $adapter->getClientModelValues();

//    $productJson = array(
//      'product_id' => $product->getId(),
//      'slug' => $product->getSlug(),
//      'name' => $product->getName(),
//      'url' => $this->get('router')->generate('product', array('slug' => $product->getSlug())),
//      'price' => $product->getPriceForUser($this->getUser()),
//      'original_price' => $product->getPrice(),
//      'old_price' => $product->getOldPrice(),
//      'image' => $imageUrl,
//      'description_short' => $product->getShortDescription()
//    );

    return new JsonResponse($productJson);
  }

  /**
   * Контроллер действия "Задать вопрос по этому товару"
   */
  public function askQuestionAction($slug, Request $request)
  {
    $product = $this->getDoctrine()
      ->getRepository('StoreBundle:Store\Catalog\Product\Product')
      ->findOneBy(array('slug' => $slug));

    if (!$product)
    {
      $this->createNotFoundException(sprintf('Товар "%s" не найден', $slug));
    }

    $questionData = json_decode($request->getContent(), true);

    $question = new ProductQuestion();
    $question->setProduct($product);

    $form = $this->createForm(ProductQuestionType::class, $question);

    $form->submit($questionData);

    if ($form->isSubmitted() && $form->isValid())
    {
      $questionData = $form->getData();

      $em = $this->getDoctrine()->getManager();

      $em->persist($questionData);
      $em->flush();

      $operatorEmail = $this->getParameter('operator_email');
      if ($operatorEmail)
      {
        $email = $this->get('aw_email_templating.template.factory')->createMessage(
          'product_question_operator',
          array($this->getParameter('mailer_from') => $this->getParameter('mailer_sender_name')),
          array($operatorEmail => ''),
          array(
            'product_name' => $product->getName(),
            'product_sku' => $product->getSku(),
            'customer_name' => $question->getName(),
            'customer_email' => $question->getEmail(),
            'question' => $question->getText()
          ));

        $this->get('mailer')->send($email);
      }

      return new JsonResponse();
    }

    return new JsonResponse(null, 400);
  }


}