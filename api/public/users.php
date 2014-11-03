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

	public function feed()
	{
		$filter = $this->request->get('filter', 'string');

		$map = array(
			'top' => 'where p.starred is true and p.deleted = false',
			'new' => 'where p.created::date = CURRENT_DATE and p.deleted = false',
			'hot' => 'where p.rating > 9 and p.deleted = false'
		);

		return $this->feed->get(array_key_exists($filter, $map) ? $map[$filter] : 'where p.deleted = false');
	}

	public function category()
	{
		$category = $this->request->get('category', 'string');
		$normalCategory = trim($category);

		return $this->feed->get("where p.detail->'category' = '" . $normalCategory . "' and p.deleted = false");
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
		$profile = strtolower($this->request->get('profile'));

		$checksum = md5($password . 'E4fgg656@#%uyghfddhghcv');

		$sql = 'insert into	users (login, password) values (:login, :checksum) returning id';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':login', $email, PDO::PARAM_STR);
		$sth->bindParam(':checksum', $checksum, PDO::PARAM_STR);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$id = $data["id"];
		if ($id > 0) {
			$this->redis->set('users:' . $id . ':info', json_encode(json_decode($profile)));
		}

		$sth->closeCursor();

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
			$rating = $this->post->rating($id, $value);
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
			$this->template->assign('comment', $result);
			$this->template->assign('post', array("id" => $post_id));
			$this->template->fetch('functions.tpl');

			$data[".wrapper[data-id=" . $post_id . "] .form-comment"] = array(
				"mode" => "before",
				"html" => $this->template->fetch("block/comment/comment.tpl")
			);
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

		$result = $this->post->pin($id, $value);

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

		$result = $this->post->star($id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			".wrapper[data-id=$id] .starred-btn span, .wrapper[data-id=$id] .starred-btn i" => array('mode' => 'swapClass', 'class' => 'text-muted'),
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
			$data["#feed-menu"] = array(
				"mode" => "after",
				"html" => $this->template->fetch("block/post/edit.tpl")
			);
		}

		return $data;
	}

	public function removepost()
	{
		$id = $this->request->get('id', 'integer');

		$result = $this->post->remove($id);

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
				$result = $this->post->edit($id, array("title" => $title, "body" => $body, "category" => $normalCategory));
			} else {
				$result = $this->post->add(array("title" => $title, "body" => $body, "category" => $normalCategory));
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
				$data[".wrapper[data-id=$result]"] = array(
					"mode" => "replaceWith",
					"html" => $this->template->fetch("block/post/post.tpl")
				);
			} else {
				$data[".wrapper[data-id=new]"] = array(
					"mode" => "replaceWith",
					"html" => $this->template->fetch("block/post/post.tpl")
				);
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
}
