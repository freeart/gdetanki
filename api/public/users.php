<?php

class Users extends Api
{
	private $current;

	public function __construct()
	{
		parent::__construct();

		$id = $this->session->get('user_id');

		if ($id > 0) {
			$data = json_decode($this->redis->get('users:' . $id . ':info'));
			$data->id = $id;

			$this->current = $data;
		}
	}

	public function profile()
	{
		return $this->current;
	}

	public function read()
	{
		$postId = $this->request->get('id', 'integer');

		return $this->post->get($postId);
	}

	public function feedCount()
	{
		$filter = $this->request->get('filter', 'string');
		$name = $this->request->get('name', 'string');

		$map = array(
			'top' => 'where p.starred is true and p.deleted = false',
			'new' => 'where p.created::date = CURRENT_DATE and p.deleted = false',
			'hot' => 'where p.rating > 9 and p.deleted = false',
			'category' => "where p.detail->'category' = '" . $name . "' and p.deleted = false"
		);

		return $this->feed->count(array_key_exists($filter, $map) ? $map[$filter] : 'where p.deleted = false');
	}

	public function feed()
	{
		$filter = $this->request->get('filter', 'string');
		$name = $this->request->get('name', 'string');

		$page = $this->request->get('page', 'integer');
		$page = $page > 0 ? $page : 1;

		$map = array(
			'top' => 'where p.starred is true and p.deleted = false',
			'new' => 'where p.created::date = CURRENT_DATE and p.deleted = false',
			'hot' => 'where p.rating > 9 and p.deleted = false',
			'category' => "where p.detail->'category' = '" . $name . "' and p.deleted = false"
		);

		return $this->feed->get(array_key_exists($filter, $map) ? $map[$filter] : 'where p.deleted = false', $page);
	}

	public function logged($str = null)
	{
		if ($str) {
			return !empty($this->current) ? $str : '/signin';
		} else {
			return !empty($this->current) && $this->current->id > 0;
		}
	}

	public function signout()
	{
		$this->session->remove('user_id');

		return array(
			"body" => array("mode" => "refresh")
		);
	}

	public function registration()
	{
		$email = strtolower($this->request->get('email', 'email'));
		$password = strtolower($this->request->get('password', 'enject'));
		$refer = $this->request->get('refer');
		$profile = $this->request->get('profile');

		$profile = json_decode($profile);

		$checksum = md5($password . 'E4fgg656@#%uyghfddhghcv');

		$id = null;

		if (!empty($email) && !empty($password) && !empty($refer) && gettype($profile) == "object") {
			$gameId = null;
			if (stripos($refer, '.swf')) {
				$matches = null;
				$returnValue = preg_match_all('#hash=(.*)&#sU', $refer, $matches);
				if ($returnValue > 0) {
					$gameId = $matches[1][0];
				}
			} else if (stripos($refer, 'friend=')) {
				$returnValue = parse_url($refer, PHP_URL_FRAGMENT);
				parse_str($returnValue);
				if (isset($friend)) {
					$gameId = $friend;
				}
			} else if (stripos($refer, '.xml')) {
				$returnValue = basename($refer, ".xml");
				if (strlen($returnValue) > 0) {
					$gameId = $returnValue;
				}
			}

			if (strlen($gameId) > 0) {
				$xmlraw = file_get_contents("http://tankionline.com/referer/" . $gameId . ".xml");
				if (!empty($xmlraw)) {
					$idIsCorrect = preg_match_all('#<callsign>(.*)</callsign>#sU', $xmlraw, $callsign);
					preg_match_all('#<rank>(.*)</rank>#sU', $xmlraw, $rank);
					preg_match_all('#<scores>(.*)</scores>#sU', $xmlraw, $scores);

					$profile->game_user = $callsign[1][0];
					$profile->game_rank = $rank[1][0];
					$profile->game_scores = $scores[1][0];

					if ($idIsCorrect) {
						$sql = 'insert into	users (login, password) values (:login, :checksum) returning id';

						$sth = $this->db->prepare($sql);

						$sth->bindParam(':login', $email, PDO::PARAM_STR);
						$sth->bindParam(':checksum', $checksum, PDO::PARAM_STR);

						$sth->execute();

						$data = $sth->fetch(PDO::FETCH_ASSOC);
						$id = $data["id"];
						if ($id > 0) {
							$this->redis->set('users:' . $id . ':info', json_encode($profile));
						}

						$sth->closeCursor();
					}
				}
			}
		}

		if ($id > 0) {
			return array(
				"body" => array("mode" => "redirect", "url" => "/signin"),
				"data" => array("id" => $id)
			);
		} else {
			return array(
				"error" => 1,
				"message" => "Ошибка регистрации"
			);
		}
	}

	public function signin()
	{
		$login = strtolower($this->request->get('login', 'enject'));
		$password = $this->request->get('password', 'enject');
		$checksum = md5($password . 'E4fgg656@#%uyghfddhghcv');

		$sql = 'select id
				from users
				where login = :login and password = :checksum';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':login', $login, PDO::PARAM_STR);
		$sth->bindParam(':checksum', $checksum, PDO::PARAM_STR);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$id = $data["id"];
		if ($id > 0) {
			$this->session->set('user_id', $id);
			//$data = json_decode($this->redis->get('users:' . $id));
			//$data->id = $id;
		}

		$sth->closeCursor();

		if ($id > 0) {
			return array(
				"body" => array("mode" => "redirect", "url" => "/"),
				"data" => array("id" => $id)
			);
		} else {
			return array(
				"error" => 1,
				"message" => "Ошибка логина или пароля"
			);
		}
	}

	public function rating()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'integer');
		$value = $value > 0 ? 1 : -1;

		$bulk = $this->redis->hmget('users:' . $this->current->id . ':rating:' . $id, 'v', 'r');
		$bulk[1] = $bulk[1] === null ? 2 : $bulk[1];

		if ($bulk[0] != $value && $bulk[1] > 0) {
			$value = $bulk[1] == 1 ? $value * 2 : $value;

			$this->redis->hmset('users:' . $this->current->id . ':rating:' . $id, 'v', $value, 'r', $bulk[1] - 1);
			$rating = $this->post->rating($this->current->id, $id, $value);
			return array(
				'[data-id=' . $id . '] .social-bar span' => array('mode' => 'replace', 'html' => $rating)
			);
		} else {
			return array(
				"error" => 1,
				"message" => $bulk[1] == 1 ? "Вы можете только изменить голос (1 раз)" : "Вы уже голосовали"
			);
		}
	}

	public function comment()
	{
		$post_id = $this->request->get('post_id', 'integer');
		$text = $this->request->get('text', 'inject');
		$author_id = $this->current->id;

		$result = $this->post->comment($post_id, $author_id, $text);

		$data = array(
			"error" => $result->id > 0 ? 0 : 1,
			"result" => $result
		);

		if ($result->id > 0) {
			$this->template->assign('this', $this);
//			$this->template->assign('comment', $result);
			$this->template->assign('post', array("id" => $post_id));
			$this->template->fetch('functions.tpl');

//			$data[".wrapper[data-id=" . $post_id . "] .form-comment"] = array(
//				"mode" => "before",
//				"html" => $this->template->fetch("block/comment/comment.tpl")
//			);
			$data[" .wrapper[data-id=" . $post_id . "] .form-comment"] = array(
				"mode" => "replaceWith",
				"html" => $this->template->fetch("block/comment/edit.tpl")
			);
		}

		return $data;
	}

	public function pin()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'boolean');

		$result = $this->post->pin($this->current->id, $id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			".wrapper[data-id=$id] .pinned-btn span, .wrapper[data-id=$id] .pinned-btn i" => array('mode' => 'swapClass', 'class' => 'text-muted'),
			"result" => $result
		);
	}

	public function star()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'boolean');

		$result = $this->post->star($this->current->id, $id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			".wrapper[data-id=$id] .starred-btn span, .wrapper[data-id=$id] .starred-btn i" => array('mode' => 'swapClass', 'class' => 'text-muted'),
			"result" => $result
		);
	}

	public function comment_enabled()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'boolean');

		$result = $this->post->comment_enabled($this->current->id, $id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			".wrapper[data-id=$id] .comment-enabled-btn span, .wrapper[data-id=$id] .comment-enabled-bth i" => array('mode' => 'swapClass', 'class' => 'text-muted'),
			"result" => $result
		);
	}

	public function revertpost()
	{
		$id = $this->request->get('id', 'integer');

		$this->template->assign('this', $this);
		if ($id > 0) {
			$this->template->assign('post', $this->post->get($id));
		}

		$this->template->fetch('functions.tpl');

		$data = array();

		if ($id > 0) {
			$data[".wrapper[data-id=$id]"] = array(
				"mode" => "replaceWith",
				"html" => $this->template->fetch("block/post/post.tpl")
			);
		} else {
			$data[".wrapper[data-id=new]"] = array(
				"mode" => "delete"
			);
		}

		return $data;
	}

	public function editpost()
	{
		$id = $this->request->get('id', 'integer');

		$this->template->assign('this', $this);
		if ($id > 0) {
			$this->template->assign('post', $this->post->get($id));
		}

		$this->template->fetch('functions.tpl');

		$data = array();

		if ($id > 0) {
			$data[".wrapper[data-id=$id]"] = array(
				"mode" => "replaceWith",
				"html" => $this->template->fetch("block/post/edit.tpl")
			);
		} else {
			$data[".wrapper[data-id=new]"] = array(
				"mode" => "delete"
			);
			$data[".feed-body"] = array(
				"mode" => "prepend",
				"html" => $this->template->fetch("block/post/edit.tpl")
			);
		}

		return $data;
	}

	public function removepost()
	{
		$id = $this->request->get('id', 'integer');

		$result = $this->post->remove($this->current->id, $id);

		return array(
			"error" => $result === true ? 0 : 1,
			".wrapper[data-id=$id]" => array('mode' => 'delete'),
			"result" => $result
		);
	}

	public function savepost()
	{
		$id = $this->request->post('id', 'integer');

		$category = $this->request->post('category', 'inject');
		$title = $this->request->post('title', 'inject');
		$body = $this->request->post('body');

		$normalCategory = trim($category);

		if (!empty($title) && !empty($body) && !empty($normalCategory)) {
			if ($id > 0) {
				$result = $this->post->edit($this->current->id, $id, array("title" => $title, "body" => $body, "category" => $normalCategory));
			} else {
				$result = $this->post->add($this->current->id, array("title" => $title, "body" => $body, "category" => $normalCategory));
			}

			$this->template->assign('this', $this);
			$this->template->assign('post', $this->post->get($result));

			$this->template->fetch('functions.tpl');
		} else {
			$result = null;
		}

		$data = array(
			"error" => $result > 0 ? 0 : 1,
			"result" => $result
		);

		if ($result > 0) {
			if ($id > 0) {
//				$data[".wrapper[data-id=$result]"] = array(
//					"mode" => "replaceWith",
//					"html" => $this->template->fetch("block/post/post.tpl")
//				);
			} else {
//				$data[".wrapper[data-id=new]"] = array(
//					"mode" => "replaceWith",
//					"html" => $this->template->fetch("block/post/post.tpl")
//				);
				$data[".wrapper[data-id=new]"] = array('mode' => 'delete');
			}
		}

		return $data;
	}

	public function distCatalogs()
	{
		$sql = 'select DISTINCT detail->\'category\' as name
				from posts';

		$sth = $this->db->prepare($sql);

		$sth->execute();

		$data = [];

		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row['name'];
		}

		$sth->closeCursor();

		return $data;
	}

	public function verify_update()
	{
		$action = $this->request->get('action', 'string');
		$entity = $this->request->get('entity', 'string');
		$post_id = $this->request->get('post_id', 'integer');
		$comment_id = $this->request->get('comment_id', 'integer');
		$current = $this->request->get('current');

		$data = array();
		if ($entity == "posts") {
			$uricomponents = parse_url($current);

			$params = array();
			if (array_key_exists('query', $uricomponents)) {
				parse_str($uricomponents['query'], $params);
			}

			$filter = null;
			if (strpos($uricomponents['path'], '/feed/') !== false) {
				$filter = str_replace(array('/', 'feed'), '', $uricomponents['path']);
			}
			if (strpos($uricomponents['path'], '/category/') !== false) {
				$filter = 'category';
				$name = urldecode(str_replace(array('/', 'category'), '', $uricomponents['path']));
			}

			$page = intval($params['page']);

			$map = array(
				'top' => 'where p.starred is true and p.deleted = false and p.id = ' . $post_id,
				'new' => 'where p.created::date = CURRENT_DATE and p.deleted = false and p.id = ' . $post_id,
				'hot' => 'where p.rating > 9 and p.deleted = false and p.id = ' . $post_id,
				'category' => "where p.detail->'category' = '" . $name . "' and p.deleted = false and p.id = " . $post_id
			);

			$posts = array();
			if ($page < 2) {
				$posts = $this->feed->verify_update(array_key_exists($filter, $map) ? $map[$filter] : 'where p.deleted = false and p.id = ' . $post_id);
			}

			if (count($posts) > 0) {
				$this->template->assign('this', $this);

				$this->template->assign('post', $this->post->get($posts[0]['id']));

				$this->template->fetch('functions.tpl');

				if ($action == 'insert') {
					$data[".feed-body"] = array(
						"mode" => "prepend",
						"html" => $this->template->fetch("block/post/post.tpl")
					);

				}
			}
		} else if ($entity == "comments") {
			$this->template->assign('this', $this);

			$comment = $this->post->getComment($comment_id);

			$this->template->assign('comment', $comment);

			$this->template->fetch('functions.tpl');

			if ($action == 'insert') {
				$data[".wrapper[data-id=" . $comment->postId . "] .comments-body"] = array(
					"mode" => "append",
					"html" => $this->template->fetch("block/comment/comment.tpl")
				);

			}
		}

		return $data;
	}
}
