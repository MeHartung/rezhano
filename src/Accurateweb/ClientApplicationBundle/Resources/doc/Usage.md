##Add adapter
```yml
#services.yml
app.cart_summary.adapter:
    class: StoreBundle\DataAdapter\Cart\CartSummaryAdapter
    tags:
      - { name: aw.client_application.adapter, alias: cart.summary }
```
####Example
```php
class CartAdapter implements ClientApplicationModelAdapterInterface
{
  //default Model name for key in ObjectCache
  public function getModelName()
  {
    return 'Cart';
  }

  /**
   * transform model to array
   * @param $cart Order
   * @return array
   */
  public function transform ($cart, $options=[])
  {
    return array(
      'subtotal' => $cart->getSubtotal(),
      'quantity' => $cart->getTotalQuantity(),
      'total' => $cart->getTotal()
    );
  }

  /**
   * @return bool
   */
  public function supports ($subject)
  {
    return $subject instanceof Order;
  }
}
```
##Twig usage
```twig
{{ cart|client_model('cart.summary', {/*options*/}, 'CartSummary') }}
```
Result:
```
ObectCache['CartSummary'] = {"subtotal":0.00, "quantity":0,"total":0};
```

```twig
{{ cart.orderItems|client_model_collection('order.item', 'OrderItems') }}
```
Result:
```
ObjectCache['OrderItems'] = [{/*item1*/}, {/*item2*/}];
```

##Php usage
```php
$this->get('aw.client_application.transformer')->getClientModelData(
  $cartItem, //object
  'order.item' //transformer alias
); //array
```
```php
$this->get('aw.client_application.transformer')->getClientModelCollectionData(
      $shippingMethods, //array of objects
      'shipping.method', //transformer alias
      ['shipment' => $shipment, 'skip_deffered' => true] //options
); //array of arrays
```