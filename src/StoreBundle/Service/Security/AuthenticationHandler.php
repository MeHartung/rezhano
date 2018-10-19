<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 19.03.18
 * Time: 13:01
 */

namespace StoreBundle\Service\Security;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AuthenticationHandler extends DefaultAuthenticationFailureHandler
  implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
  use TargetPathTrait;

  public function onAuthenticationSuccess(Request $request, TokenInterface $token)
  {
    if ($request->isXmlHttpRequest())
    {
      return new JsonResponse([], 200);
    }

    if ($request->getSession() && $request->getSession()->has('_security.main.target_path'))
    {
      $targetPath = $this->getTargetPath($request->getSession(), 'main');
      $this->removeTargetPath($request->getSession(), 'main');
      return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->httpUtils->generateUri($request, '/'));
  }

  /**
   * onAuthenticationFailure
   *
   * @param  Request $request
   * @param  AuthenticationException $exception
   */
  public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
  {
    if ($request->isXmlHttpRequest())
    {
      return new JsonResponse(['errors' => [
        '_global' => 'Неверный логин или пароль',
      ]], 400);
    }

    return parent::onAuthenticationFailure($request, $exception);

  }

}