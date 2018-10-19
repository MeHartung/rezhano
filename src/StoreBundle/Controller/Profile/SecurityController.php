<?php

namespace StoreBundle\Controller\Profile;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
  /**
   * @param Request $request
   *
   * @return Response
   */
  public function loginAction (Request $request)
  {
    /** @var $session Session */
    $session = $request->getSession();

    $authErrorKey = Security::AUTHENTICATION_ERROR;
    $lastUsernameKey = Security::LAST_USERNAME;

    // get the error if any (works with forward and redirect -- see below)
    if ($request->attributes->has($authErrorKey))
    {
      $error = $request->attributes->get($authErrorKey);
    }
    elseif (null !== $session && $session->has($authErrorKey))
    {
      $error = $session->get($authErrorKey);
      $session->remove($authErrorKey);
    }
    else
    {
      $error = null;
    }

    if (!$error instanceof AuthenticationException)
    {
      $error = null; // The value does not come from the security component.
    }

    // last username entered by the user
    $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

    $csrfToken = $this->has('security.csrf.token_manager')
      ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
      : null;

    return $this->renderLogin(array(
      'last_username' => $lastUsername,
      'error' => $error,
      'csrf_token' => $csrfToken,
    ));
  }

  protected function renderLogin(array $data)
  {
    return $this->render('@Store/Profile/login.html.twig', $data);
  }
}