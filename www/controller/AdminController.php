<?php

namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\Models\User;
use App\Models\Comment;

class AdminController implements ControllerProviderInterface
{
	
	/**
	 * Registers paths
	 * 
	 * @param Application $app		Current app
	 * 
	 * @return array
	 */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory']; 
		
		$controllers->post('/login', [$this, 'loginAction'])->bind('admin_login');
		$controllers->match('/logout', [$this, 'logoutAction'])->bind('admin_logout');
		$controllers->get('/', [$this, 'indexAction'])->bind('admin');
		$controllers->get('/{current_page}', [$this, 'indexAction'])->bind('page')->assert('current_page', '\d+');

        return $controllers;
    }
	
	public function logoutAction(Application $app) {
		
		$app['session']->set('user', null);
		
		return $app->redirect($app['url_generator']->generate('homepage'));		
	}	
	
	public function loginAction(Application $app) {
		
		if ($app['session']->get('user') !== null) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => 'You have already logged in!'
				]
			], 400);
		}						
		
		$statement = $app['pdo']->prepare('SELECT * FROM `' . User::getTable() . '` WHERE email = :email AND password = :password LIMIT 1');
		$statement->setFetchMode(\PDO::FETCH_CLASS, '\\App\\Models\\User');
		
		if (!$statement->execute([':email' => $_POST['email'], ':password' => sha1($_POST['password'])])) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => 'Bad login data!'
				]
			], 401);
		}
		
		$app['session']->set('user', $statement->fetch ());				
		
		return \Symfony\Component\HttpFoundation\JsonResponse::create([				
			], 200);
	}
	
	public function indexAction(Application $app, $current_page = 1) {
		
		$user = $app['session']->get('user');
		
		if ($user === null) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => 'You must login to access this functionality'
				]
			], 401);
		}
		
		$comments_count = isset($_COOKIE['comments_count'])?(int)$_COOKIE['comments_count']:5;
		if ($comments_count < 5) {
			$comments_count = 5;
		} elseif ($comments_count > 2000) {
			$comments_count = 2000;
		}
		
		$count = $app['pdo']->query('SELECT COUNT(*) FROM `'.Comment::getTable(). '`')->fetchColumn(); 
		$pages = round($count / $comments_count);
		
		$offset = (($current_page -1 )*$comments_count);
		if ($offset >= $count) {
			$offset = ($pages - 1) * $comments_count;
			$current_page = $pages;
		}
		
		$app['session']->set('current_page', $current_page);
		$app['session']->set('pages', $pages);
		$app['session']->set('comments_count', $comments_count);
		
		$statement = $app['pdo']->query('SELECT * FROM `'.Comment::getTable().'` LIMIT '.($offset).','.$comments_count.';', \PDO::FETCH_CLASS, '\\App\\Models\\Comment');
		
		if ($statement instanceof \PDOStatement) {
			$comments = $statement->fetchAll();
		} else {
			$comments = [];
		}			
		
		return $app['twig']->render('admin.twig', compact('comments', 'comments_count', 'pages', 'current_page', 'user'));
	}	
	
}