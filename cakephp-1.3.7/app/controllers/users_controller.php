<?php

App::import('Lib', 'StringManipulation');

class UsersController extends AppController {
  public $components = array('Openid', 'RequestHandler');
  
  public function login() {
    $realm = 'http://' . $_SERVER['HTTP_HOST'];
    $returnTo = $realm . Router::url('/users/login');
    
    if ($this->RequestHandler->isPost() && !$this->Openid->isOpenIDResponse()) {
      try {
        $this->Openid->authenticate($this->data['OpenidUrl']['openid'], $returnTo, $realm);
      } catch (InvalidArgumentException $e) {
        $this->set('error', 'Invalid OpenID');
      } catch (Exception $e) {
        $this->set('error', $e->getMessage());
      }
    } elseif ($this->Openid->isOpenIDResponse()) {
      $response = $this->Openid->getResponse($returnTo);
      
      if ($response->status == Auth_OpenID_CANCEL) {
        $this->set('error', 'Verification cancelled');
      } elseif ($response->status == Auth_OpenID_FAILURE) {
        $this->set('error', 'OpenID verification failed: ' . $response->message);
      } elseif ($response->status == Auth_OpenID_SUCCESS) {
        echo 'successfully authenticated!';
        $user = $this->User->findByOpenid($response->identity_url);
        if (!$user) {
          $this->User->create();
          $this->User->set('openid', $response->identity_url);
          $this->User->save();
          $user = $this->User->read();
        }
        $this->Session->write("user", $user);
        $this->redirect("/");
        exit;
      }
    }
  }

  public function logout() {
    if ($this->Session->valid()) {
      $this->Session->destroy();
      $this->redirect('/');
    }
  }

  public function index() {
    $sessionUser = $this->Session->read('user');
    if (!$sessionUser || !$sessionUser['User']['admin']) {
      $this->Session->setFlash(_('Only admins can see the user list.'), 'flash_failure');
      $this->redirect('/');
      exit();
    }
    if (empty($this->data)) {
      $this->set('users', $this->User->find('all', array('order' => 'openid asc')));
      $this->set('myId', $sessionUser['User']['id']);
    } else {
      foreach ($this->data['User'] as $name => $value) {
        if (string_startsWith($name, 'admin_')) {
          $this->User->id = substr($name, 6);
          $this->User->read();
          $this->User->set('admin', $value);
          $this->User->save();
        }
      }
      $this->redirect('');
      exit();
    }
  }
}

?>
