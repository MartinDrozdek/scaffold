<?php //netteCache[01]000404a:2:{s:4:"time";s:21:"0.45273800 1398037451";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:64:"C:\webserver\apache\htdocs\bc2\nette-blog\app\config\config.neon";i:2;i:1398034598;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:70:"C:\webserver\apache\htdocs\bc2\nette-blog\app\config\config.local.neon";i:2;i:1398034525;}}}?><?php
// source: C:\webserver\apache\htdocs\bc2\nette-blog\app/config/config.neon 
// source: C:\webserver\apache\htdocs\bc2\nette-blog\app/config/config.local.neon 

/**
 * @property Nette\Application\Application $application
 * @property Nette\Caching\Storages\FileStorage $cacheStorage
 * @property Nette\DI\Container $container
 * @property Nette\Http\Request $httpRequest
 * @property Nette\Http\Response $httpResponse
 * @property Nette\DI\Extensions\NetteAccessor $nette
 * @property Nette\Application\IRouter $router
 * @property Nette\Http\Session $session
 * @property Nette\Security\User $user
 */
class SystemContainer extends Nette\DI\Container
{

	protected $meta = array(
		'types' => array(
			'nette\\object' => array(
				'nette',
				'nette.cacheJournal',
				'cacheStorage',
				'nette.httpRequestFactory',
				'httpRequest',
				'httpResponse',
				'nette.httpContext',
				'session',
				'nette.userStorage',
				'user',
				'application',
				'nette.presenterFactory',
				'nette.mailer',
				'nette.database.default',
				'nette.database.default.context',
				'22_App_Model_UserManager',
				'container',
			),
			'nette\\di\\extensions\\netteaccessor' => array('nette'),
			'nette\\caching\\storages\\ijournal' => array('nette.cacheJournal'),
			'nette\\caching\\storages\\filejournal' => array('nette.cacheJournal'),
			'nette\\caching\\istorage' => array('cacheStorage'),
			'nette\\caching\\storages\\filestorage' => array('cacheStorage'),
			'nette\\http\\requestfactory' => array('nette.httpRequestFactory'),
			'nette\\http\\irequest' => array('httpRequest'),
			'nette\\http\\request' => array('httpRequest'),
			'nette\\http\\iresponse' => array('httpResponse'),
			'nette\\http\\response' => array('httpResponse'),
			'nette\\http\\context' => array('nette.httpContext'),
			'nette\\http\\session' => array('session'),
			'nette\\security\\iuserstorage' => array('nette.userStorage'),
			'nette\\http\\userstorage' => array('nette.userStorage'),
			'nette\\security\\user' => array('user'),
			'nette\\application\\application' => array('application'),
			'nette\\application\\ipresenterfactory' => array('nette.presenterFactory'),
			'nette\\application\\presenterfactory' => array('nette.presenterFactory'),
			'nette\\application\\irouter' => array('router'),
			'nette\\mail\\imailer' => array('nette.mailer'),
			'nette\\mail\\sendmailmailer' => array('nette.mailer'),
			'nette\\database\\connection' => array('nette.database.default'),
			'nette\\database\\context' => array('nette.database.default.context'),
			'nette\\security\\iauthenticator' => array('22_App_Model_UserManager'),
			'app\\model\\usermanager' => array('22_App_Model_UserManager'),
			'app\\routerfactory' => array('23_App_RouterFactory'),
			'nette\\di\\container' => array('container'),
		),
	);


	public function __construct()
	{
		parent::__construct(array(
			'appDir' => 'C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app',
			'wwwDir' => 'C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\www\\scaffold',
			'debugMode' => FALSE,
			'productionMode' => TRUE,
			'environment' => 'production',
			'consoleMode' => TRUE,
			'container' => array(
				'class' => 'SystemContainer',
				'parent' => 'Nette\\DI\\Container',
				'accessors' => TRUE,
			),
			'tempDir' => 'C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app/../temp',
		));
	}


	/**
	 * @return App\Model\UserManager
	 */
	public function createService__22_App_Model_UserManager()
	{
		$service = new App\Model\UserManager($this->getService('nette.database.default.context'));
		return $service;
	}


	/**
	 * @return App\RouterFactory
	 */
	public function createService__23_App_RouterFactory()
	{
		$service = new App\RouterFactory;
		return $service;
	}


	/**
	 * @return Nette\Application\Application
	 */
	public function createServiceApplication()
	{
		$service = new Nette\Application\Application($this->getService('nette.presenterFactory'), $this->getService('router'), $this->getService('httpRequest'), $this->getService('httpResponse'));
		$service->catchExceptions = TRUE;
		$service->errorPresenter = 'Error';
		Nette\Application\Diagnostics\RoutingPanel::initializePanel($service);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceCacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app/../temp/cache', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return Nette\DI\Container
	 */
	public function createServiceContainer()
	{
		return $this;
	}


	/**
	 * @return Nette\Http\Request
	 */
	public function createServiceHttpRequest()
	{
		$service = $this->getService('nette.httpRequestFactory')->createHttpRequest();
		if (!$service instanceof Nette\Http\Request) {
			throw new Nette\UnexpectedValueException('Unable to create service \'httpRequest\', value returned by factory is not Nette\\Http\\Request type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Response
	 */
	public function createServiceHttpResponse()
	{
		$service = new Nette\Http\Response;
		return $service;
	}


	/**
	 * @return Nette\DI\Extensions\NetteAccessor
	 */
	public function createServiceNette()
	{
		$service = new Nette\DI\Extensions\NetteAccessor($this);
		return $service;
	}


	/**
	 * @return Nette\Forms\Form
	 */
	public function createServiceNette__basicForm()
	{
		$service = new Nette\Forms\Form;
		trigger_error('Service nette.basicForm is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Caching\Cache
	 */
	public function createServiceNette__cache($namespace = NULL)
	{
		$service = new Nette\Caching\Cache($this->getService('cacheStorage'), $namespace);
		trigger_error('Service cache is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileJournal
	 */
	public function createServiceNette__cacheJournal()
	{
		$service = new Nette\Caching\Storages\FileJournal('C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app/../temp');
		return $service;
	}


	/**
	 * @return Nette\Database\Connection
	 */
	public function createServiceNette__database__default()
	{
		$service = new Nette\Database\Connection('mysql:host=127.0.0.1;dbname=test', 'root', 'root', array('lazy' => TRUE));
		$service->setContext(new Nette\Database\Context($service, new Nette\Database\Reflection\DiscoveredReflection($service, $this->getService('cacheStorage')), $this->getService('cacheStorage')));
		Nette\Diagnostics\Debugger::getBlueScreen()->addPanel('Nette\\Database\\Diagnostics\\ConnectionPanel::renderException');
		return $service;
	}


	/**
	 * @return Nette\Database\Context
	 */
	public function createServiceNette__database__default__context()
	{
		$service = $this->getService('nette.database.default')->getContext();
		if (!$service instanceof Nette\Database\Context) {
			throw new Nette\UnexpectedValueException('Unable to create service \'nette.database.default.context\', value returned by factory is not Nette\\Database\\Context type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Context
	 */
	public function createServiceNette__httpContext()
	{
		$service = new Nette\Http\Context($this->getService('httpRequest'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Nette\Http\RequestFactory
	 */
	public function createServiceNette__httpRequestFactory()
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy(array());
		return $service;
	}


	/**
	 * @return Nette\Latte\Engine
	 */
	public function createServiceNette__latte()
	{
		$service = new Nette\Latte\Engine;
		return $service;
	}


	/**
	 * @return Nette\Mail\Message
	 */
	public function createServiceNette__mail()
	{
		$service = new Nette\Mail\Message;
		trigger_error('Service nette.mail is deprecated.', 16384);
		$service->setMailer($this->getService('nette.mailer'));
		return $service;
	}


	/**
	 * @return Nette\Mail\SendmailMailer
	 */
	public function createServiceNette__mailer()
	{
		$service = new Nette\Mail\SendmailMailer;
		return $service;
	}


	/**
	 * @return Nette\Application\PresenterFactory
	 */
	public function createServiceNette__presenterFactory()
	{
		$service = new Nette\Application\PresenterFactory('C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app', $this);
		$service->setMapping(array(
			'*' => 'App\\*Module\\Presenters\\*Presenter',
		));
		return $service;
	}


	/**
	 * @return Nette\Templating\FileTemplate
	 */
	public function createServiceNette__template()
	{
		$service = new Nette\Templating\FileTemplate;
		$service->registerFilter($this->getService('nette.latte'));
		$service->registerHelperLoader('Nette\\Templating\\Helpers::loader');
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\PhpFileStorage
	 */
	public function createServiceNette__templateCacheStorage()
	{
		$service = new Nette\Caching\Storages\PhpFileStorage('C:\\webserver\\apache\\htdocs\\bc2\\nette-blog\\app/../temp/cache', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return Nette\Http\UserStorage
	 */
	public function createServiceNette__userStorage()
	{
		$service = new Nette\Http\UserStorage($this->getService('session'));
		return $service;
	}


	/**
	 * @return Nette\Application\IRouter
	 */
	public function createServiceRouter()
	{
		$service = $this->getService('23_App_RouterFactory')->createRouter();
		if (!$service instanceof Nette\Application\IRouter) {
			throw new Nette\UnexpectedValueException('Unable to create service \'router\', value returned by factory is not Nette\\Application\\IRouter type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Session
	 */
	public function createServiceSession()
	{
		$service = new Nette\Http\Session($this->getService('httpRequest'), $this->getService('httpResponse'));
		$service->setExpiration('14 days');
		return $service;
	}


	/**
	 * @return Nette\Security\User
	 */
	public function createServiceUser()
	{
		$service = new Nette\Security\User($this->getService('nette.userStorage'), $this->getService('22_App_Model_UserManager'));
		return $service;
	}


	public function initialize()
	{
		date_default_timezone_set('Europe/Prague');
		Nette\Caching\Storages\FileStorage::$useDirectories = TRUE;
		$this->getByType("Nette\Http\Session")->exists() && $this->getByType("Nette\Http\Session")->start();
		header('X-Frame-Options: SAMEORIGIN');
		@header('X-Powered-By: Nette Framework');
		@header('Content-Type: text/html; charset=utf-8');
		Nette\Utils\SafeStream::register();
	}

}
