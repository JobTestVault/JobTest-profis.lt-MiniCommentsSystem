<?php

namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\Models\Comment;

class CommentsController  implements ControllerProviderInterface
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
		
		$controllers->post('/', [$this, 'postAction'])->bind('comment');
		$controllers->delete('/{id}', [$this, 'deleteAction'])->bind('delete_comment')->assert('id', '\d+');

        return $controllers;
    }
				
	public function postAction(Application $app) {						
		$comment = new Comment($_POST);
		$comment->created_at = date('Y-m-d H:i:s');
		
		$errors = $comment->validate();
		if (!empty($errors)) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => $errors
			], 400);
		}
		
		$sql = 'INSERT INTO `'. Comment::getTable().'` (`email`, `comment`, `name`, `created_at`) VALUE(:email, :comment, :name, :created_at)';
		
		$statement = $app['pdo']->prepare($sql);
		$comment->bind($statement);
		
		if (!$statement->execute()) {
			$err = $app['pdo']->errorInfo();
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => $err[2]
				]
			], 500);
		}
		
		$comment->id = (int)$app['pdo']->lastInsertId();

		return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'comments' => $app['twig']->render('comment.twig', compact('comment'))
			], 201);
	}
	
	public function deleteAction(Application $app, $id) {
		
		if ($app['session']->get('user') === null) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => 'You must login to access this functionality'
				]
			], 401);
		}
		
		if (!$app['pdo']->exec('DELETE FROM `'. Comment::getTable().'` WHERE `id` = ' .  $id)) {
			return \Symfony\Component\HttpFoundation\JsonResponse::create([
				'errors' => [
					'global' => 'Comment was not deleted'
				]
			], 500);
		}
		
		$current_page = (int)$app['session']->get('current_page');
		$comments_count = (int)$app['session']->get('comments_count');
		$pages = (int)$app['session']->get('pages');
		
		$offset = (($current_page -1 )*$comments_count);
		
		$statement = $app['pdo']->query('SELECT * FROM `'.Comment::getTable().'` LIMIT '.($offset).','.$comments_count.';', \PDO::FETCH_CLASS, '\\App\\Models\\Comment');
		
		if ($statement instanceof \PDOStatement) {
			$comments = $statement->fetchAll();
		} else {
			$comments = [];
		}		
		
		return \Symfony\Component\HttpFoundation\JsonResponse::create([
			'comments' => $app['twig']->render('admin_comments_block.twig', compact('comments')),
			'pagination' => $app['twig']->render('pagination.twig', compact('current_page', 'pages'))
		]);
	}
	
	
}