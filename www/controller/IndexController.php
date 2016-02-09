<?php

namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use App\Models\Comment;

class IndexController  implements ControllerProviderInterface
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
		
		$controllers->get('/', [$this, 'indexAction'])->bind('homepage');
		$controllers->get('/{current_page}', [$this, 'indexAction'])->bind('page')->assert('current_page', '\d+');
		$controllers->post('/set/limit/{current_page}', [$this, 'setLimitAction'])->bind('set_comments_limit');

        return $controllers;
    }
	
	public function setLimitAction(Application $app, $current_page = 1) {
		$limit = (int)$_POST['limit'];
						
		$count = $app['pdo']->query('SELECT COUNT(*) FROM `'.Comment::getTable(). '`')->fetchColumn(); 
		$pages = round($count / $limit);		
		
		$offset = (($current_page -1 )*$limit);
		if ($offset >= $count) {
			$offset = ($pages - 1) * $limit;
			$current_page = $pages;
		}		

		$statement = $app['pdo']->query('SELECT * FROM `'.Comment::getTable().'` LIMIT '.($offset).','.$limit.';', \PDO::FETCH_CLASS, '\\App\\Models\\Comment');
		
		if ($statement instanceof \PDOStatement) {
			$comments = $statement->fetchAll();
		} else {
			$comments = [];
		}
		
		$html = '';
		foreach ($comments as $comment) {
			$html .= $app['twig']->render('comment.twig', compact('comment'));
		}
		
		$cookie = new Cookie("comments_count", $limit);
		$response = \Symfony\Component\HttpFoundation\JsonResponse::create([
			'comments' => $html,
			'pagination' => $app['twig']->render('pagination.twig', compact('current_page', 'pages'))
		]);
		$response->headers->setCookie($cookie);
		return $response;
	}
		
	public function indexAction(Application $app, $current_page = 1) {
		
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
		
		$statement = $app['pdo']->query('SELECT * FROM `'.Comment::getTable().'` LIMIT '.($offset).','.$comments_count.';', \PDO::FETCH_CLASS, '\\App\\Models\\Comment');
		
		if ($statement instanceof \PDOStatement) {
			$comments = $statement->fetchAll();
		} else {
			$comments = [];
		}			
		
		return $app['twig']->render('main.twig', compact('comments', 'comments_count', 'pages', 'current_page'));
	}
	
}