<?php

namespace qck\core;

/**
 * App class is essentially the class to start.
 * 
 * @author muellerm
 */
class App
{

  function __construct( \qck\core\interfaces\AppConfigFactory $ConfigFactory )
  {
    $this->ConfigFactory = $ConfigFactory;
  }

  function run()
  {
    // warnings as errors
    $exErrHandler = new ExceptionErrorHandler();
    $exErrHandler->install();

    // now load appConfigig local values
    $Config = $this->getAppConfig();
    $exErrHandler->setAppConfig( $Config );

    // let the frontcontroller handle the rest
    $cntrllrFctry = $Config->getControllerFactory();
    /* @var $cntrllr \qck\core\interfaces\Controller */
    $cntrllr = $cntrllrFctry->getController();

    // handle error if no controller is found
    if ( is_null( $cntrllr ) )
      throw new \Exception( "Controller ".$cntrllrFctry->getCurrentControllerClassName()." was not found", \qck\core\interfaces\Response::CODE_PAGE_NOT_FOUND );

    /* @var $response \qck\core\interfaces\Response */
    $response = $cntrllr->run( $Config );
    
    // send the response
    // if there is a null response, the controller has sent everything himself
    if(!is_null($response))
      $response->send();
  }

  /**
   * 
   * @return \qck\core\interfaces\AppConfig
   */
  public function getAppConfig()
  {
    if ( is_null( $this->Config ) )
    {
      $this->Config = $this->ConfigFactory->create();
    }
    return $this->Config;
  }

  /**
   *
   * @var \qck\core\interfaces\AppConfigFactory
   */
  protected $ConfigFactory;
  protected $Config;

}
